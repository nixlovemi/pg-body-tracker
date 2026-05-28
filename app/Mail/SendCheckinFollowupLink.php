<?php

namespace App\Mail;

use App\Models\Client;

class SendCheckinFollowupLink extends BaseMail
{
    public function __construct(
        public Client $Client,
        public string $link,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.pages.checkin.mail.link.emailTitle'),
            'TITLE' => __('messages.pages.checkin.mail.link.title'),
            'HEADER_IMG_FULL' => '/public/images/mail-avaliation-link-' . strtolower($this->Client->gender) . '.jpg',
            'ARR_TEXT_LINES' => [
                __('messages.pages.checkin.mail.link.bodyLine1', ['clientName' => $this->Client->getName()]),
                __('messages.pages.checkin.mail.link.bodyLine2'),
                __('messages.pages.checkin.mail.link.bodyLine3'),
                __('messages.pages.checkin.mail.link.bodyLine4'),
            ],
            'ACTION_BUTTON_URL' => $this->link,
            'ACTION_BUTTON_TEXT' => __('messages.pages.checkin.mail.link.actionButton'),
        ]);
    }
}
