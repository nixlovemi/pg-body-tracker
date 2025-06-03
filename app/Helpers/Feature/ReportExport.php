<?php

namespace App\Helpers\Feature;

class ReportExport extends FeatureAbstract
{
    public function getName(): string
    {
        return 'Report Export';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.ReportExport.validateMessage');
    }
}
