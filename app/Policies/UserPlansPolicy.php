<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPlans;

class UserPlansPolicy
{
    public function view(User $user, UserPlans $plan): bool
    {
        return $user->isRoot() || $plan->user_id === $user->id;
    }

    public function update(User $user, UserPlans $plan): bool
    {
        return $this->view($user, $plan);
    }
}
