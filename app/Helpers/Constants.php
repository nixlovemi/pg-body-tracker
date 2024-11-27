<?php

namespace App\Helpers;

final class Constants {
    public const USER_DEFAULT_IMAGE_PATH = '/template/start-bootstrap/img/undraw_profile.svg';
    public const REGEX_PHONE_NUMBER = '/(?=.*[0-9])[- +()0-9]+/';
    public const FORM_ADD = 'add';
    public const FORM_EDIT = 'edit';
    public const FORM_VIEW = 'view';
    public const FORM_ACTIONS = [
        self::FORM_ADD,
        self::FORM_EDIT,
        self::FORM_VIEW,
    ];
}
