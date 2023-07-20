<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function before($user, $ability)
{
    if ($user->isAdmin()) {
        return true;
    }
}

public function create(User $user)
{
    return $user->isAdmin();
}

public function update(User $user, News $news)
{
    return $user->isAdmin();
}

public function delete(User $user, News $news)
{
    return $user->isAdmin();
}
}
