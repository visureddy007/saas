<?php

/**
 * HomeController.php - Controller file
 *
 * This file is part of the Home component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Home\Controllers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use App\Yantrana\Base\BaseRequest;
use Endroid\QrCode\Writer\PngWriter;
use App\Yantrana\Base\BaseController;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode;
use App\Yantrana\Support\CommonRequest;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Yantrana\Components\Home\HomeEngine;
use Endroid\QrCode\Writer\ValidationException;
use App\Yantrana\Components\Page\PageEngine;



class HomeController extends BaseController
{
    /**
     * @var HomeEngine - Home Engine
     */
    protected $homeEngine;
    /**
     * @var PageEngine - Home Engine
     */
    protected $pageEngine;
  
    /**
     * Constructor
     *
     * @param  HomeEngine  $homeEngine  - Home Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(HomeEngine $homeEngine,PageEngine $pageEngine)
    {
        $this->homeEngine = $homeEngine;
        $this->pageEngine = $pageEngine;
    }

    public function homePageView()
    {
        if(getAppSettings('other_home_page_url') and (trim(getAppSettings('other_home_page_url'), '/') != trim(url()->current(), '/'))) {
            return redirect()->away(getAppSettings('other_home_page_url'));
        }
        return $this->loadView(getAppSettings('current_home_page_view'));
    }

    //Contact for view load
    public function contactForm()
    {
        return $this->loadView('contact.contact',[], [
            'compress_page' => false
        ]);
    }

    public function contactProcess(CommonRequest $request)
    {
        $request->validate([
            'email' => 'required|email' . (getAppSettings('disallow_disposable_emails') ? '|indisposable' : ''),
            'full_name' => 'required|min:2|max:100',
            'subject' => 'required|min:2|max:100',
            'message' => 'required|min:5',
        ]);
        $processReaction = $this->homeEngine->processContactEmail($request->all());

        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.contact.form', [], [
                    __tr('Thank you for contacting us, your request has been submitted successfully, we will get back to you soon.'),
                    'success',
                ])
            );
        }

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Compiled JS file from server mostly contains the translated words/sentences for the javascript
     *
     * @return view
     */
    public function serverCompiledJs()
    {
        return response()->view('server-compiled-js')->header('Content-Type', 'text/javascript');
    }

    public function noActivePlan()
    {
        return response()->view('errors.no-active-plan');
    }
    public function viewTermsAndPolicies($contentName)
    {
        $validItems = [
            'user_terms' => __tr('User terms'),
            'vendor_terms' => __tr('Vendor terms'),
            'privacy_policy' => __tr('Privacy Policy'),
        ];
        abortIf(!array_key_exists($contentName, $validItems));
        return response()->view('terms-policies', [
            'contentName' => $contentName,
            'validItems' => $validItems,
        ]);
    }

    public function generateWhatsAppQR($vendorUid = null, $phoneNumber = null)
    {
        if(!$vendorUid or !$phoneNumber) {
            return null;
        }
        $this->generateUrlQR("https://wa.me/{$phoneNumber}", true);
    }
    public function generateUrlQR($upiAddress, $logo = null)
    {
        $writer = new PngWriter();
        // Create QR code
        $qrCode = QrCode::create($upiAddress)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        if($logo) {
            // Create generic logo
            $logo = Logo::create(public_path('imgs/Digital_Glyph_Green.png'))
            ->setResizeToWidth(50)
            ->setPunchoutBackground(true)
            ;
        }

        // Create generic label
        $label = Label::create(__tr(''))->setTextColor(new Color(255, 0, 0));
        $result = $writer->write($qrCode, $logo, $label);
        // Validate the result
        // $writer->validateResult($result, 'Life is too short to be generating QR codes');
        // Directly output the QR code
        header('Content-Type: '.$result->getMimeType());
        echo $result->getString();
        // Save it to a file
        // $result->saveToFile(__DIR__.'/qrcode.png');
        // return $result->getDataUri();
        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        // return '<img src="'. $result->getDataUri() .'" alt="WhatsApp QR Code">';
    }

    public function generateUpiPaymentUrl(BaseRequest $request)
    {

        $this->generateUrlQR(base64_decode($request->url));
    }
    /**
     * preview page
     *---------------------------------------------------------------- */
    public function previewPage($pageUid, $title)
    {
        $processReaction = $this->pageEngine->previewPage($pageUid);

        return $this->loadView('page.preview', $processReaction['data'], [
            'compress_page' => false,
        ]);
    }
}