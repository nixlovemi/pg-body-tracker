<?php

namespace App\Policies;

use App\Models\Avaliation;
use App\Models\User;

class AvaliationPolicy
{
    public function view(User $user, Avaliation $avaliation): bool
    {
        return $user->isRoot() || $avaliation->client?->user_id === $user->id;
    }

    public function update(User $user, Avaliation $avaliation): bool
    {
        return $this->view($user, $avaliation);
    }

    public function share(User $user, Avaliation $avaliation): bool
    {
        return $this->view($user, $avaliation);
    }
}
