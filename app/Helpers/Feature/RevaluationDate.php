<?php

namespace App\Helpers\Feature;

class RevaluationDate extends FeatureAbstract
{
    public function getName(): string
    {
        return 'Revaluation Date';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.RevaluationDate.validateMessage');
    }
}
