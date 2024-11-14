<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorreoSacaSm extends Model
{
    protected $table = 'corrsaca_sm';

    protected $primaryKey = 'id';

    protected $fillable = [
        'codsm',
        'correo',
        'corrsaca_id',
        'estatus',
        'estatus_admin',
        'ult_proce',
        'henvio',
    ];

    public $timestamps = false;

    /**
     * Get the CorreoSaca that owns the CorreoSacaSm
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function correoSaca() : BelongsTo
    {
        return $this->belongsTo(CorreoSaca::class, 'corrsaca_id');
    }

    /**
     * Get the Licencia that owns the CorreoSacaSm
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licencia() : BelongsTo
    {
        return $this->belongsTo(Licencias::class, 'codsm', 'codigo');
    }

}
