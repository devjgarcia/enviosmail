<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EstadoLicencia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Licencias extends Model 
{
    protected $table = 'lice_sm';

    protected $fillable = [
        'id',
        'prefijo',
        'codigo',
        'frkcliente',
        'frkproducto',
        'frkcanal',
        'inicio',
        'vencimiento',
        'status',
        'creacion',
        'actualizacion',
        'empresa',
        'rsocial',
        'rif',
        'tipo_producto',
        'correo',
        'telefono',
        'codigo_pais',
        'nombre_pais'
    ];

    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'inicio' => 'date:d-m-Y',
        'actualizacion' => 'date:d-m-Y',
        'creacion' => 'date:d-m-Y',
    ];

    protected $appends = [
        'estado_licencia',
        'class_estado',
        'vencimiento_format',
    ];

    /**
     * Get the status descrip.
     *
     * @return string
     */
    public function getEstadoLicenciaAttribute()
    {
        return EstadoLicencia::getEstado( $this->status );
    }

    /**
     * Get the vencimiento format d-m-Y.
     *
     * @return string
     */
    public function getVencimientoFormatAttribute()
    {
        return Carbon::parse($this->vencimiento)->format('d-m-Y');
    }

    public function getClassEstadoAttribute()
    {
        return EstadoLicencia::getClassEstado( $this->status );
    }

    /**
     * Get the CorreoSacaSm for the Licencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function correoSacaSm(): HasMany
    {
        return $this->hasMany(CorreoSacaSm::class, 'codsm', 'codigo');
    }

}
