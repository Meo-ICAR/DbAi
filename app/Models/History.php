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
        'submission_date',
        'message',
        'sqlstatement',
        'charttype',
        'dashboardorder',
        'nviewed',
        'masterquery',
        'slavedashboard',
        'database_name',
        'categorymenu_id'  // Add category menu reference
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'slavedashboard' => 'integer',
    ];

    /**
     * Get the master query that this history record belongs to.
     */
    public function master()
    {
        return $this->belongsTo(History::class, 'masterquery');
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
