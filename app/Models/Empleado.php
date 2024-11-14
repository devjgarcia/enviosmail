<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empleado extends Model
{
    protected $connection = 'mysql_din';
    
    protected $table = 'tg016_empleado';

    protected $primaryKey = 'co_empleado';

    public $timestamps = false;

    /**
     * Get the GrupoEmpleado that owns the GrupoSupervisores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupoSupervisores(): BelongsTo
    {
        return $this->belongsTo(GrupoEmpleado::class, 'id_tn003');
    }
}
