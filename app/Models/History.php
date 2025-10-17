<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\DatabaseScope; // Importa lo Scope
use Illuminate\Database\Eloquent\Factories\HasFactory;


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
        'database_name'  // Add this line
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
