<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->getKey() === $comment->author->getKey();
    }
}
