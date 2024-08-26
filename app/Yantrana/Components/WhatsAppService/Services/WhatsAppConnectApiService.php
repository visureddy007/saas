<?php
/**
* WhatsAppConnectApiService.php -
*
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\WhatsAppService\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Yantrana\Base\BaseEngine;
use Illuminate\Support\Facades\Http;
use App\Yantrana\Components\WhatsAppService\Interfaces\WhatsAppServiceEngineInterface;

class WhatsAppConnectApiService extends BaseEngine implements WhatsAppServiceEngineInterface
{
    protected $baseApiRequestEndpoint = 'https://graph.facebook.com/v20.0/'; // Base Request endpoint

    protected $waAccountId; // WhatsApp Business Account ID
    protected $whatsAppPhoneNumberId; // Phone number ID
    protected $accessToken; // Access token
    protected $vendorId = null;

    /**
     * Constructor
     *
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(
    ) {
    }

    /**
     * Configure settings based on vendor id
     *
     * @param string $serviceItem
     * @return mixed
     */
    protected function getServiceConfiguration($serviceItem)
    {
        return getVendorSettings($serviceItem, null, null, $this->vendorId ?: getVendorId());
    }
    /**
     * Undocumented function
     *
     * @param [type] $request
     * @link https://developers.facebook.com/docs/whatsapp/embedded-signup
     * @link https://developers.facebook.com/docs/whatsapp/embedded-signup/errors
     * @return EngineResponse
     */
    public function processEmbeddedSignUp($request)
    {
        updateProgressTextModel(
            __tr('preparing ...')
        );
        $accessToken = null;
        $phoneNumberId = null;
        $wabaId = null;
        // simulate zone
        // $accessToken = '';
        // $phoneNumberId = '';
        // $wabaId = '';
        // simulate zone ends
        if(!$wabaId) {
            $wabaId = $request->waba_id;
        }
        if(!$accessToken) {
            updateProgressTextModel(
                __tr('generating token ...')
            );
            // https://developers.facebook.com/docs/facebook-login/guides/access-tokens#apptokens
            $tokenData = $this->apiPostRequest("{$this->baseApiRequestEndpoint}oauth/access_token", [
                'client_id' => getAppSettings('embedded_signup_app_id'),
                'client_secret' => getAppSettings('embedded_signup_app_secret'),
                'code' => $request->request_code,
            ]);
            $this->accessToken = $tokenData['access_token'] ?? null;
            abortIf(!$this->accessToken ?? null, 402, __tr('Failed to get Access token'));
        } else {
            $this->accessToken = $accessToken;
        }
        if(!$phoneNumberId) {
            updateProgressTextModel(
                __tr('verifying phone number records ...')
            );
            $phoneNumberId = $request->phone_number_id;
            // get phone number records already registered with the whatsapp business account
            $phoneNumbers = $this->getPhoneNumbers($wabaId);
            // get the record of current requested phone number id
            $phoneNumberRecord = Arr::first(($phoneNumbers['data'] ?? []), function ($value, $key) use (&$phoneNumberId) {
                return $value['id'] == $phoneNumberId;
            });
            // if the record not found we may need to register this
            if($phoneNumberRecord['platform_type'] != 'CLOUD_API') {
                updateProgressTextModel(
                    __tr('registering phone number ...')
                );
                // https://developers.facebook.com/docs/whatsapp/cloud-api/reference/registration
                // DEVELOPER NOTE: to avoid number blocked for certain period as for testing multiple times, comment this line for a while
                $phoneRegistration = $this->apiPostRequest("{$this->baseApiRequestEndpoint}/{$request->phone_number_id}/register", [
                        'messaging_product' => 'whatsapp',
                        'pin' => '123456',
                    ]);
                abortIf((!$phoneRegistration['success'] ?? null), 402, __tr('Failed Phone number registration'));
                updateProgressTextModel(
                    __tr('fetching updated phone number records ...')
                );
                // https://developers.facebook.com/docs/whatsapp/embedded-signup/manage-accounts/phone-numbers
                $phoneNumbers = $this->getPhoneNumbers($wabaId);
                $phoneNumberRecord = Arr::first(($phoneNumbers['data'] ?? []), function ($value, $key) use (&$phoneNumberId) {
                    return $value['id'] == $phoneNumberId;
                });
            }
        }
        // __logDebug('phoneNumbers', $phoneNumberRecord);
        $vendorUid = getVendorUid();
        $vendorId = getVendorId();
        updateProgressTextModel(
            __tr('subscribing events ...')
        );
        // vendor webhook
        $webhookUrl = getViaSharedUrl(route('vendor.whatsapp_webhook', [
            'vendorUid' => $vendorUid,
        ]));
        // https://developers.facebook.com/docs/whatsapp/embedded-signup/webhooks/override#delete-waba-alternate-callback
        $subscribeToWebhook = $this->apiPostRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps");
        $webhookOverride = $this->apiPostRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps", [
            "override_callback_uri" => $webhookUrl,
            "verify_token" => sha1($vendorUid)
        ]);
        // __logDebug('webhookOverride', $webhookOverride);
        abortIf(!$webhookOverride['success'] ?? null, 402, __tr('Failed register a webhook'));
        updateProgressTextModel(
            __tr('fetching events subscriptions ...')
        );
        // fetch existing records
        $webhookOverrides = $this->apiGetRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps");
        updateProgressTextModel(
            __tr('finalizing ...')
        );
        $dataToUpdate = [
            'embedded_setup_done_at' => now(),
            'facebook_app_id' => getAppSettings('embedded_signup_app_id'), //Arr::get($webhookOverrides, 'data.0.whatsapp_business_api_data.id'),
            'whatsapp_access_token' => $this->accessToken,
            'whatsapp_business_account_id' => $wabaId,
            'current_phone_number_number' => cleanDisplayPhoneNumber($phoneNumberRecord['display_phone_number']),
            'current_phone_number_id' => $phoneNumberId,
            // 'webhook_verified_at' => now(), // as it will be done automatically
            'webhook_messages_field_verified_at' => now(),
            'whatsapp_phone_numbers_data' => $phoneNumbers,
            'whatsapp_onboarding_raw_data' => [
                'waba_id' => $wabaId,
                'phone_number_id' => $phoneNumberId,
                'webhook_overrides' => $webhookOverrides,
            ]
        ];
        app()->make(\App\Yantrana\Components\Vendor\VendorSettingsEngine::class)->updateProcess('whatsapp_cloud_api_setup', $dataToUpdate, $vendorId);
        updateClientModels([
            'isSetupInProcess' => false
        ]);
        sleep(2);
        updateProgressTextModel(
            __tr('It\'s done!!')
        );
        return $this->engineSuccessResponse([], __tr('You are now connected to WhatsApp Cloud API'));
    }
    /**
     * Get the phone numbers of WhatsApp Business ID
     *
     * @param int $wabaId
     * @return void
     *
     * @link https://developers.facebook.com/docs/graph-api/reference/whats-app-business-account-to-number-current-status/#Reading
     */
    public function getPhoneNumbers($wabaId, $accessToken = null)
    {
        if($accessToken) {
            $this->accessToken = $accessToken;
        }
        return $this->apiGetRequest("{$this->baseApiRequestEndpoint}{$wabaId}/phone_numbers?fields=display_phone_number,certificate,name_status,new_certificate,new_name_status,last_onboarded_time", []) ?? null;
    }

    // as of now not in use
    public function generateAppAccessToken($appId, $appSecret)
    {
        $appAccessToken = $this->apiGetRequest("https://graph.facebook.com/oauth/access_token", [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'grant_type' => 'client_credentials',
        ]);
    }

    public function connectWebhookOverrides($vendorUid, $wabaId)
    {
        // vendor webhook
        $webhookUrl = getViaSharedUrl(route('vendor.whatsapp_webhook', [
           'vendorUid' => $vendorUid,
        ]));
        // https://developers.facebook.com/docs/whatsapp/embedded-signup/webhooks/override#delete-waba-alternate-callback
        $this->apiPostRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps");
        return $this->apiPostRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps", [
            "override_callback_uri" => $webhookUrl,
            "verify_token" => sha1($vendorUid)
        ]);
    }

    /**
     * Remove existing webhooks
     *
     * @param int $wabaId
     * @param string $accessToken
     * @return array
     *
     * @link https://developers.facebook.com/docs/whatsapp/embedded-signup/webhooks/override#delete-waba-alternate-callback
     * @link https://developers.facebook.com/docs/graph-api/webhooks/getting-started/webhooks-for-whatsapp#delete-a-subscription
     */
    public function removeExistingWebhooks($wabaId, $accessToken = null)
    {
        if($accessToken) {
            $this->accessToken = $accessToken;
        }
        if(!$this->accessToken) {
            $this->accessToken = getVendorSettings('whatsapp_access_token');
        }
        // delete existing webhooks
        $this->apiDeleteRequest("{$wabaId}/subscribed_apps");
        return $this->apiPostRequest("{$this->baseApiRequestEndpoint}{$wabaId}/subscribed_apps");
    }

    /**
     * Setup App WhatsApp Webhook
     *
     * @param int $appId
     * @param string $appSecret
     * @return array
     *
     * @link https://developers.facebook.com/docs/graph-api/reference/v2.5/app/subscriptions/#--app-id--subscriptions
     */
    public function connectBaseWebhook($appId, $appSecret, $vendorUid = 'service-whatsapp')
    {
        $webhookUrl = getViaSharedUrl(route('vendor.whatsapp_webhook', [
            'vendorUid' => $vendorUid,
        ]));
        $subscriptions = $this->apiPostRequest("{$this->baseApiRequestEndpoint}" . $appId . "/subscriptions?access_token=" . $appId . "|" . $appSecret, [
            'object' => 'whatsapp_business_account',
            'fields' => 'messages,message_template_quality_update,message_template_status_update,account_update',
            'callback_url' => $webhookUrl,
            "verify_token" => sha1($vendorUid)
        ]);
        return $subscriptions;
    }
    /**
     * Delete App WhatsApp Webhook
     *
     * @param int $appId
     * @param string $appSecret
     * @return array
     *
     * @link https://developers.facebook.com/docs/graph-api/reference/v20.0/app/subscriptions#delete
     */
    public function disconnectBaseWebhook($appId, $appSecret, $wabaId = null)
    {
        if($wabaId) {
            $this->removeExistingWebhooks($wabaId);
        }
        return $this->apiDeleteRequest($appId . "/subscriptions?access_token=" . $appId . "|" . $appSecret, [
            'object' => 'whatsapp_business_account',
            'fields' => 'messages,message_template_quality_update,message_template_status_update,account_update',
        ]);
    }

    /**
     * Debug token to get information about it
     *
     * @param int $appId
     * @param string $appSecret
     * @param string $inputToken
     * @return array
     */
    public function debugTokenInfo($appId, $appSecret, $inputToken)
    {
        return $this->apiGetRequest("{$this->baseApiRequestEndpoint}/debug_token", [
            'access_token' => $appId . "|" . $appSecret,
            'input_token' => $inputToken,
        ]);
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * Below are the BASE Requests like get,post, delete etc
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * Manual API requests
     *
     * @return array
     */
    protected function apiGetRequest(string $requestSubject, array $parameters = [])
    {
        return $this->baseApiRequest()->get("{$requestSubject}", $parameters)->json();
    }

    /**
     * Manual API requests
     *
     * @return array
     */
    protected function apiPostRequest(string $requestSubject, array $parameters = [])
    {
        return $this->baseApiRequest()->post("$requestSubject", array_merge(
            [
                // 'messaging_product' => 'whatsapp',
                // 'recipient_type' => 'individual',
            ],
            $parameters
        ))->json();
    }

    /**
     * Manual API requests
     *
     * @return array
     */
    protected function apiDeleteRequest(string $requestSubject, array $parameters = [])
    {
        return $this->baseApiRequest()->delete("{$this->baseApiRequestEndpoint}/$requestSubject", $parameters)->json();
    }

    /**
     * Base API requests
     *
     * @return Http query request
     */
    protected function baseApiRequest()
    {
        // $this->getServiceConfiguration('whatsapp_access_token')
        return Http::withToken($this->accessToken)->throw(function ($response, $e) {
            $getContents = $response->getBody()->getContents();
            $getContentsDecoded = json_decode($getContents, true);
            $userMessage = Arr::get($getContentsDecoded, 'error.error_user_title', '') . ' '
            . Arr::get($getContentsDecoded, 'error.message', '') . ' '
            . Arr::get($getContentsDecoded, 'error.error_user_msg', '') . ' '
            . Arr::get($getContentsDecoded, 'error.error_data.details');
            if(!$userMessage) {
                $userMessage = $e->getMessage();
            }
            // __logDebug($userMessage);
            if(!ignoreFacebookApiError()) {
                // stop and response back for error if any
                abortIf(
                    true,
                    $response->status(),
                    $userMessage
                );
            }
        });
    }
}
