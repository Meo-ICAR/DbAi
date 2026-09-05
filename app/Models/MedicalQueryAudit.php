<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalQueryAudit extends Model
{
    protected $connection = 'dbai';

    protected $table = 'medical_query_audits';

    protected $fillable = [
        'user_id',
        'action',
        'domanda',
        'sql',
        'esito',
        'righe',
        'warning',
        'assunzioni',
        'errore',
        'ip',
        'database_name',
    ];

    protected $casts = [
        'righe' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
