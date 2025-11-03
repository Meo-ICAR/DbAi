<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $connection = 'dbai';
    protected $table = 'chat_history';
    
    protected $fillable = [
        'thread_id',
        'messages'
    ];
    
    protected $casts = [
        'messages' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
