<?php

namespace App\Policies;

use App\Models\ChatHistory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Only allow viewing any if they can view the index
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChatHistory $chatHistory): bool
    {
        return $user->id === $chatHistory->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChatHistory $chatHistory): bool
    {
        return $user->id === $chatHistory->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChatHistory $chatHistory): bool
    {
        return $user->id === $chatHistory->user_id;
    }
}
