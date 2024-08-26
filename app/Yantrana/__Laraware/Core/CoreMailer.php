<?php

namespace App\Yantrana\__Laraware\Core;

use Mail;

/**
 * Core Mailer - 1.0.3 - 03 NOV 2022
 *
 * Core Mailer for Angulara (Laraware) applications
 *
 * @since 0.1.227 - 09 JAN 2017
 *--------------------------------------------------------------------------- */
abstract class CoreMailer
{
    /**
     * This method use for send mail to given recipients.
     *
     * @param  array  $mailData
     * @return bool
     *---------------------------------------------------------------- */
    public function send($mailData = [])
    {
        extract($mailData);

        // Generating email view as html file instead of sending __email.html
        if (config('laraware.mail_view_debug', false) == true) {
            $emailsTemplate = isset($messageData['emailsTemplate']) ? $messageData['emailsTemplate'] : $view;

            $emailViewToGenerate = fopen(public_path('__email.html'), 'w') or exit('Unable to open file!');
            $prependString = '<style>body{ margin:0;}</style><div style="padding:4px; text-align:center;background: #F3F3D9;
    margin: 0;margin-bottom: 20px;">Generated for <strong>'.$emailsTemplate.'</strong> on </div><br>';
            $mailTemplateData = view($view, $messageData);

            fwrite($emailViewToGenerate, $prependString.$mailTemplateData->render());
            fclose($emailViewToGenerate);

            config([
                'app.__emailDebugView' => url('__email.html'),
            ]);

            return true;
        }

        $mailRecipients = [
            'recipients' => (! empty($recipients)) ? $recipients : '',
            'cc' => (! empty($cc)) ? $cc : '',
            'bcc' => (! empty($bcc)) ? $bcc : '',
        ];

        //get recipients
        $recipients = $this->getMailRecipents($mailRecipients);

        $mailFrom = isset($from) ? $from : config('mail.from.address');

        $mailReplyTo = __ifIsset($replyTo) ? $replyTo : [];

        $emailSent = Mail::send(
            $view,
            $messageData,
            function ($message) use ($recipients, $subject, $mailFrom, $mailReplyTo) {
                // Check for if direct recipients exist
                if (! empty($recipients['to'])) {
                    if (is_array($recipients['to']) and isset($recipients['to'][1])) {
                        $message->to($recipients['to'][0], $recipients['to'][1]);
                    } else {
                        $message->to($recipients['to']);
                    }
                }

                // Check for if carbon copy recipients exist
                if (! empty($recipients['cc'])) {
                    $message->cc($recipients['cc']);
                }

                // Check for if blind carbon copy recipients exist
                if (! empty($recipients['bcc'])) {
                    $message->bcc($recipients['bcc']);
                }

                // Check for if sender is array collection
                if (is_array($mailFrom)) {
                    $message->from($mailFrom[0], $mailFrom[1]);
                } else {
                    $message->from($mailFrom);
                }

                // Check if relay to exist then set replay to email
                if (! __isEmpty($mailReplyTo)) {
                    if (is_array($mailReplyTo)) {
                        $message->replyTo($mailReplyTo[0], $mailReplyTo[1]);
                    } else {
                        $message->replyTo($mailReplyTo);
                    }
                }

                $message->subject($subject);
            }
        );

        if (empty($emailSent->failedRecipients)) {
            return true;
        }

        return false;
    }

    /**
     * This method use for get recipients.
     *
     * @param  array  $recipients
     * @return array
     *---------------------------------------------------------------- */
    protected function getMailRecipents($getRecipients = [])
    {
        $mailRecipents = [];

        $mailRecipents['to'] = $mailRecipents['cc'] =
            $mailRecipents['bcc'] = [];

        if (! is_array($getRecipients['recipients'])) {
            // get commas separated recipients using getRecipentsArray
            $mailRecipents['to'] = $this->getRecipents($getRecipients['recipients']);
        } else {
            // check direct recipients
            if (isset($getRecipients['recipients'])) {
                $mailRecipents['to'] = $this->getRecipents(
                    $getRecipients['recipients']
                );
            }
        }

        // check carbon copy recipients
        if (isset($getRecipients['cc'])) {
            $mailRecipents['cc'] = $this->getRecipents(
                $getRecipients['cc']
            );
        }

        // check blind carbon copy recipients
        if (isset($getRecipients['bcc'])) {
            $mailRecipents['bcc'] = $this->getRecipents(
                $getRecipients['bcc']
            );
        }

        return $mailRecipents;
    }

    /**
     * This method use for explode commas separated values in array.
     *
     * @param  sting  $recipentString.
     * @return array.
     */
    protected function getRecipents($recipentString = null)
    {
        $recipentsArray = [];
        if (! empty($recipentString)) {
            $recipentsArray = explode(',', $recipentString);
        }

        return $recipentsArray;
    }
}
