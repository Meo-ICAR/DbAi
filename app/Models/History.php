<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\DatabaseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CategoryMenu;


class History extends Model
{
    use HasFactory;

    protected $connection = 'dbai'; // Specifica il nome della seconda connessione

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Applica il nostro scope globale
        static::addGlobalScope(new DatabaseScope);
    }

    protected $fillable = [
        'user_id',
        'share_token',
        'share_expires_at',
        'submission_date',
        'message',
        'sqlstatement',
        'charttype',
        'dashboardorder',
        'nviewed',
        'masterquery',
        'slavedashboard',
        'database_name',
        'categorymenu_id'
    ];

    protected $dates = [
        'submission_date',
        'share_expires_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'slavedashboard' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include records for the current user.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->when(auth()->check(), function($q) {
            return $q->where('user_id', auth()->id());
        });
    }

    /**
     * Get the master query that this history record belongs to.
     */
    public function master()
    {
        return $this->belongsTo(History::class, 'masterquery');
    }

    /**
     * Generate a shareable link for this history item
     *
     * @param int $hours Number of hours until the link expires (null for no expiration)
     * @return string The shareable URL
     */
    public function generateShareLink($hours = 24)
    {
        $this->update([
            'share_token' => \Illuminate\Support\Str::random(40),
            'share_expires_at' => $hours ? now()->addHours($hours) : null
        ]);

        return route('history.share', $this->share_token);
    }

    /**
     * Check if the history item is currently shareable
     *
     * @return bool
     */
    public function isShareable()
    {
        return $this->share_token && 
               (!$this->share_expires_at || $this->share_expires_at->isFuture());
    }

    /**
     * Get the user that owns the history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the slave queries for this master query.
     */
    public function slaves()
    {
        return $this->hasMany(History::class, 'masterquery');
    }

    /**
     * Get the category menu that this history belongs to.
     */
    public function categoryMenu()
    {
        return $this->belongsTo(CategoryMenu::class, 'categorymenu_id');
    }

    /**
     * Scope a query to find a history by SQL statement.
     */
    public function scopeWhereSqlStatement($query, $sql)
    {
        return $query->on('dbai')->where('sqlstatement', $sql);
    }

    /**
     * Get the most recent histories.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('submission_date', 'desc')->limit($limit);
    }
}
