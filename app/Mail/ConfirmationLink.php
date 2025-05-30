<?php

namespace App\Mail;

class ConfirmationLink extends BaseMail
{
    public function __construct(
        public string $fullUserName,
        public string $actionButtonURL,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.models.User.ConfirmationLink.subject'),
            'TITLE' => __('messages.models.User.ConfirmationLink.subject'),
            'HEADER_IMG_FULL' => '/public/images/logo-azul.png',
            'ARR_TEXT_LINES' => [
                __('messages.models.User.ConfirmationLink.line1', ['name' => $this->fullUserName]),
                __('messages.models.User.ConfirmationLink.line2'),
                __('messages.models.User.ConfirmationLink.line3'),
            ],
            'ACTION_BUTTON_URL' => $this->actionButtonURL,
            'ACTION_BUTTON_TEXT' => __('messages.models.User.ConfirmationLink.actionLink'),
        ]);
    }
}
