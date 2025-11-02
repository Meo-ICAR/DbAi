<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $connection = 'dbai'; // Specifica il nome della connessione
    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('permission.table_names.roles'));
    }

    /**
     * The users that belong to the role.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            'dbai.role_user',
            'role_id',
            'user_id'
        )->withPivot('created_at', 'updated_at')
         ->withTimestamps();
    }
}
