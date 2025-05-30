<?php

namespace App\Mail;

use App\Models\Avaliation;

class SendAvaliationLink extends BaseMail
{
    public function __construct(
        public Avaliation $Avaliation,
        public string $link,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.pages.avaliation.sendAvaliationLink.emailTitle'),
            'TITLE' => __('messages.pages.avaliation.sendAvaliationLink.title'),
            'HEADER_IMG_FULL' => "/public/images/mail-avaliation-link-{$this->Avaliation->client->gender}.jpg",
            'ARR_TEXT_LINES' => [
                __('messages.pages.avaliation.sendAvaliationLink.bodyLine1', ['clientName' => $this->Avaliation->client->getName()]),
                __('messages.pages.avaliation.sendAvaliationLink.bodyLine2'),
                __('messages.pages.avaliation.sendAvaliationLink.bodyLine3'),
                __('messages.pages.avaliation.sendAvaliationLink.bodyLine4'),
            ],
            'ACTION_BUTTON_URL' => $link,
            'ACTION_BUTTON_TEXT' => __('messages.pages.avaliation.sendAvaliationLink.mailActionLink'),
        ]);
    }
}
