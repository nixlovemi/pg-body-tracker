<?php

namespace App\Helpers\Feature;

class CheckinFollowUp extends FeatureAbstract
{
    public function getName(): string
    {
        return 'Checkin Follow-up';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.CheckinFollowUp.validateMessage');
    }
}
