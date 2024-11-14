<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GrupoSupervisores extends Model
{
    protected $connection = 'mysql_din';
    
    protected $table = 'tn005_sub_niveles_supervisores';

    protected $primaryKey = 'id_tn005';

    public $timestamps = false;

    /**
     * Get the GrupoEmpleado that owns the GrupoSupervisores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupoEmpleado(): BelongsToMany
    {
        return $this->belongsToMany(GrupoEmpleado::class, 'id_tn003');
    }

    /**
     * Get the Empleado that owns the GrupoSupervisores
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function empleado(): HasOne
    {
        return $this->hasOne(Empleado::class, 'co_empleado');
    }
}
