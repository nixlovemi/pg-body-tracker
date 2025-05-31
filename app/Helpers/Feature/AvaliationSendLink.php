<?php

namespace App\Helpers\Feature;

class AvaliationSendLink extends FeatureAbstract
{
    public function getName(): string
    {
        return 'Avaliation Send Link';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.AvaliationSendLink.validateMessage');
    }
}
