<?php

namespace App\Policies;

use App\Models\Insight;
use App\Models\User;

class InsightPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Insight $insight): bool
    {
        return $user->id === $insight->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Insight $insight): bool
    {
        return $user->id === $insight->user_id;
    }

    public function delete(User $user, Insight $insight): bool
    {
        return $user->id === $insight->user_id;
    }
}