<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoEmpleado extends Model
{
    protected $connection = 'mysql_din';
    
    protected $table = 'tn003_sub_niveles';

    protected $primaryKey = 'id_tn003';

    public $timestamps = false;

    /**
     * Get all of the Supervisores for the GrupoEmpleado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grupoSupervisores(): HasMany
    {
        return $this->hasMany(GrupoSupervisores::class, 'id_tn003', 'id_tn003');
    }

    /**
     * Get all of the Trabajadores for the GrupoEmpleado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grupoTrabajadores(): HasMany
    {
        return $this->hasMany(GrupoTrabajadores::class, 'id_tn003', 'id_tn003');
    }
}
