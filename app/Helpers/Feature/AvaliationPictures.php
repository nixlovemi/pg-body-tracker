<?php

namespace App\Helpers\Feature;

class AvaliationPictures extends FeatureAbstract
{
    public function getName(): string
    {
        return 'Avaliation Pictures';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.AvaliationPictures.validateMessage');
    }
}
