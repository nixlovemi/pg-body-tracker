<?php

namespace App\Mail;

use App\Models\Avaliation;
use App\Models\Client;

class CheckinSubmittedNotification extends BaseMail
{
    public function __construct(
        public Client $Client,
        public Avaliation $Avaliation,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.pages.checkin.mail.submitted.emailTitle'),
            'TITLE' => __('messages.pages.checkin.mail.submitted.title'),
            'HEADER_IMG_FULL' => '/public/images/mail-avaliation-link-' . strtolower($this->Client->gender) . '.jpg',
            'ARR_TEXT_LINES' => [
                __('messages.pages.checkin.mail.submitted.bodyLine1', ['clientName' => $this->Client->getName()]),
                __('messages.pages.checkin.mail.submitted.bodyLine2', ['date' => $this->Avaliation->getFormattedDate()]),
                __('messages.pages.checkin.mail.submitted.bodyLine3'),
            ],
            'ACTION_BUTTON_URL' => route('app.client.edit', ['codedId' => $this->Client->codedId]),
            'ACTION_BUTTON_TEXT' => __('messages.pages.checkin.mail.submitted.actionButton'),
        ]);
    }
}
