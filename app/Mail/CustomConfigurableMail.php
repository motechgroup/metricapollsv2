<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomConfigurableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bodyContent;
    public $attachmentData;
    public $attachmentName;
    public $attachmentMime;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $body
     * @param array $placeholders
     * @param mixed $attachmentData
     * @param string|null $attachmentName
     * @param string|null $attachmentMime
     */
    public function __construct($subject, $body, $placeholders = [], $attachmentData = null, $attachmentName = null, $attachmentMime = null)
    {
        $this->subject = $this->replacePlaceholders($subject, $placeholders);
        $this->bodyContent = $this->replacePlaceholders($body, $placeholders);
        $this->attachmentData = $attachmentData;
        $this->attachmentName = $attachmentName;
        $this->attachmentMime = $attachmentMime;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $siteTitle = \App\Models\Setting::getValue('site_title', 'Metrica Polls');
        $siteFooter = \App\Models\Setting::getValue('site_footer', '© ' . date('Y') . ' Metrica Polls. All rights reserved.');
        $supportEmail = \App\Models\Setting::getValue('support_email', 'support@metricapolls.com');

        $htmlWrapper = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$this->subject}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f5f7;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a;
            padding: 24px;
            text-align: center;
            border-bottom: 4px solid #3b82f6;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 32px;
            line-height: 1.6;
            font-size: 15px;
        }
        .content p {
            margin: 0 0 16px;
        }
        .content a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }
        .button-container {
            margin: 24px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0 0 8px;
        }
        .footer a {
            color: #64748b;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{$siteTitle}</h1>
            </div>
            <div class="content">
                {$this->bodyContent}
            </div>
            <div class="footer">
                <p>{$siteFooter}</p>
                <p>Need support? Contact us at <a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
                <p style="font-size: 10px; color: #94a3b8; margin-top: 16px;">This is an automated system notification from {$siteTitle}. Please do not reply directly to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        $mail = $this->html($htmlWrapper);

        if ($this->attachmentData && $this->attachmentName) {
            $mail->attachData($this->attachmentData, $this->attachmentName, [
                'mime' => $this->attachmentMime ?? 'application/pdf',
            ]);
        }

        return $mail;
    }

    /**
     * Replace {placeholder} with actual values.
     */
    private function replacePlaceholders($text, $placeholders)
    {
        foreach ($placeholders as $key => $val) {
            $text = str_replace('{' . $key . '}', $val, $text);
        }
        return $text;
    }
}
