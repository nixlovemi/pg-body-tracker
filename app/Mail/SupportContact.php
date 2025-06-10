<?php

namespace App\Mail;

use App\Models\User;

class SupportContact extends BaseMail
{
    public function __construct(
        public User $user,
        public string $_subject,
        public string $_message,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => 'SUPORTE CONTATO ('.$this->_subject.')',
            'TITLE' => 'SUPORTE CONTATO ('.$this->_subject.')',
            'HEADER_IMG_FULL' => null,
            'ARR_TEXT_LINES' => [
                'Usuário: '. $this->user->getFullName(),
                'Email: '. $this->user->email,
                'Mensagem:<br />'. nl2br($this->_message),
            ],
            'ACTION_BUTTON_URL' => null,
            'ACTION_BUTTON_TEXT' => null,
        ]);
    }
}
