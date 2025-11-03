<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    protected $connection = 'dbai';
    protected $table = 'chat_history';
    
    protected $fillable = [
        'user_id',
        'thread_id',
        'messages'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'messages' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the chat history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include popular users.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
