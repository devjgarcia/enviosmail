<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrupoTrabajadores extends Model
{
    protected $connection = 'mysql_din';
    
    protected $table = 'tn005_sub_niveles_empleados';

    protected $primaryKey = 'id_tn004';

    public $timestamps = false;

    /**
     * Get the GrupoEmpleado that owns the GrupoSupervisores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupoEmpleado(): BelongsTo
    {
        return $this->belongsTo(GrupoEmpleado::class, 'id_tn003');
    }
}
