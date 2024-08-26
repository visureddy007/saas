<?php

namespace App\Yantrana\Base;

use App\Yantrana\__Laraware\Core\CoreMailer;
use App\Yantrana\Components\User\Repositories\UserRepository;
use Exception;
use Log;

/**
 * Base Mailer
 *--------------------------------------------------------------------------- */
class BaseMailer extends CoreMailer
{
    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param  UserRepository  $userRepository  - User Repository
     *-----------------------------------------------------------------------*/
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Notify Administrator.
     *
     * @param  sting  $subject.
     * @param  sting  $emailView.
     * @param  array  $messageData.
     * @return array.
     */
    public function notifyAdmin($subject, $emailView, $messageData = [], $messageType = 1)
    {
        $messageData['emailsTemplate'] = 'emails.'.$emailView;

        $messageData['mailForAdmin'] = true;
        $messageData['mailForCustomer'] = false;

        $adminEmails = [
            1 => getAppSettings('contact_email'),
            2 => getAppSettings('contact_email'),
        ];

        if (__isEmpty($adminEmails[$messageType])) {
            return false;
        }

        try {
            return $this->send([
                'recipients' => $adminEmails[$messageType],
                'subject' => $subject, //$subject,
                'view' => 'emails.index',
                'from' => $adminEmails[$messageType],
                'messageData' => $messageData,
                'replyTo' => __ifIsset($messageData['senderEmail'])
                    ? [$messageData['senderEmail'], $messageData['userName']]
                    : [],
            ]);
        } catch (Exception $e) {
            if (env('APP_DEBUG', false)) {
                Log::debug($e->getMessage());
            }

            return false;
        }
    }

    /**
     * Notify Customer.
     *
     * @param  sting  $subject.
     * @param  sting  $emailView.
     * @param  array  $messageData.
     * @param  mixed  $customerEmailOrId.
     * @return array.
     */
    public function notifyToUser($subject, $emailView, $messageData = [], $customerEmailOrId = null)
    {
        $customerName = isset($messageData['name']) ? $messageData['name'] : null;

        if (isLoggedIn()) {
            $userAuthInfo = getUserAuthInfo();
            $customerEmail = $userAuthInfo['profile']['email'];
            $customerName = $userAuthInfo['profile']['full_name'];
        }
        // if customer email or id sent
        if ($customerEmailOrId) {
            // set it as customer email address
            $customerEmail = $customerEmailOrId;

            // if its a user id then find user & get email address of it
            if (is_numeric($customerEmailOrId)) {
                if (str_contains($customerEmailOrId, '@')) {
                    $userInfo = $this->userRepository->fetchIt([
                        'email' => $customerEmailOrId,
                    ]);
                } else {
                    $userInfo = $this->userRepository->fetchIt($customerEmailOrId);
                }

                $customerEmail = $userInfo->email;
                $customerName = $userInfo->first_name.' '.$userInfo->last_name;
            }
        }

        if (! $customerEmail) {
            __logDebug('Customer Email is required for - ' . $customerEmailOrId);
            return false;
        }

        if (! $customerName) {
            $customerName = $customerEmail;
        }

        $messageData['emailsTemplate'] = 'emails.'.$emailView;
        $messageData['mailForAdmin'] = false;
        $messageData['mailForCustomer'] = true;

        try {
            return $this->send([
                'recipients' => $customerEmail,
                'replyTo' => configItem('mail_from'),
                'subject' => $subject,
                'view' => 'emails.index',
                'from' => configItem('mail_from'),
                'messageData' => $messageData,
            ]);
        } catch (Exception $e) {
            __logDebug($e->getMessage());
            return false;
        }
    }
}
