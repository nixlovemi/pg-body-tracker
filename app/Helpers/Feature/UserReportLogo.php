<?php

namespace App\Helpers\Feature;

class UserReportLogo extends FeatureAbstract
{
    public function getName(): string
    {
        return 'User Report Logo';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.UserReportLogo.validateMessage');
    }
}
