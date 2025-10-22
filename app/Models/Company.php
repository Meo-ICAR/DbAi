<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\DatabaseScope; // Importa lo Scope
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use SoftDeletes;
    protected $connection = 'dbai'; // Specifica il nome della seconda connessione

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'urlogo',
        'url_attivazione',
        'email_admin',
        'db_secrete',
        'db_connection',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'db_password',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the database connection for the model.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        // If this is a new model and hasn't been saved yet, use the default connection
        if (!$this->exists) {
            return parent::getConnection();
        }

        // Otherwise, use the company's specific database connection
        config([
            'database.connections.company_database' => [
                'driver' => $this->db_connection ?? 'mysql',
                'host' => $this->db_host,
                'port' => $this->db_port,
                'database' => $this->db_database,
                'username' => $this->db_username,
                'password' => $this->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
            ]
        ]);

        return parent::resolveConnection('company_database');
    }
}
