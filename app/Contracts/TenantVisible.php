<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface TenantVisible
{
    public function scopeVisibleTo(Builder $query, User $user): Builder;
}
