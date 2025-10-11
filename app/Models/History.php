<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'submission_date',
        'message',
        'sqlstatement',
        'charttype',
        'dashboardorder',
        'nviewed'
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    /**
     * Scope a query to find a history by SQL statement.
     */
    public function scopeWhereSqlStatement($query, $sql)
    {
        return $query->where('sqlstatement', $sql);
    }

    /**
     * Get the most recent histories.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('submission_date', 'desc')->limit($limit);
    }
}
