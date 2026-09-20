<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function view(User $user, Client $client): bool
    {
        return $user->isRoot() || $client->user_id === $user->id;
    }

    public function update(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }
}
