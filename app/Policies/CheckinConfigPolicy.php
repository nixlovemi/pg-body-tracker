<?php

namespace App\Policies;

use App\Models\CheckinConfig;
use App\Models\User;

class CheckinConfigPolicy
{
    public function view(User $user, CheckinConfig $config): bool
    {
        return $user->isRoot() || $config->client?->user_id === $user->id;
    }

    public function update(User $user, CheckinConfig $config): bool
    {
        return $this->view($user, $config);
    }
}
