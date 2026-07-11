<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomConfigurableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bodyContent;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $body
     * @param array $placeholders
     */
    public function __construct($subject, $body, $placeholders = [])
    {
        $this->subject = $this->replacePlaceholders($subject, $placeholders);
        $this->bodyContent = $this->replacePlaceholders($body, $placeholders);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->html($this->bodyContent);
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
