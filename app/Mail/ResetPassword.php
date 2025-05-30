<?php

namespace App\Mail;

class ResetPassword extends BaseMail
{
    public function __construct(
       public string $shortResetLink,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.pages.login.forgot.mailTitle'),
            'TITLE' => __('messages.pages.login.forgot.mailTitle'),
            'HEADER_IMG_FULL' => '/public/images/mail-forgot-password.jpg',
            'ARR_TEXT_LINES' => [
                __('messages.pages.login.forgot.mailLine1'),
                __('messages.pages.login.forgot.mailLine2'),
                __('messages.pages.login.forgot.mailLine3'),
            ],
            'ACTION_BUTTON_URL' => $this->shortResetLink,
            'ACTION_BUTTON_TEXT' => __('messages.pages.login.forgot.mailActionLink'),
        ]);
    }
}
