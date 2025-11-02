<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryMenu extends Model
{
    use HasFactory;
    
    protected $connection = 'dbai';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get all histories associated with this category.
     */
    public function histories()
    {
        return $this->hasMany(History::class, 'categorymenu_id');
    }
}
