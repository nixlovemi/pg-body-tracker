<?php

namespace App\Helpers\Feature;

class ClientLimit extends FeatureAbstract
{
    public const LIMIT = 5;

    public function getName(): string
    {
        return 'Client Limit';
    }

    public function validate(): bool
    {
        if (app()->runningInConsole()) {
            // for migrations, seeders, etc. we allow access
            return true;
        }

        if ($this->getPlanType() === self::FEATURE_PLAN_TYPE_PREMIUM) {
            return true;
        }

        // check if this user has created less Clients than the limit
        $clientCount = $this->User->clients()->count();
        return $clientCount < self::LIMIT;
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.ClientLimit.validateMessage', ['limit' => self::LIMIT]);
    }
}
