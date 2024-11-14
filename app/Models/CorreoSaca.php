<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorreoSaca extends Model
{
    protected $table = 'corrsaca';

    protected $fillable = [
        'id',
        'descripcion',
    ];

    /**
     * Get all of the CorreoSacaSm for the CorreoSaca
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function correoSacaSm(): HasMany
    {
        return $this->hasMany(CorreoSacaSm::class, 'corrsaca_id', 'id');
    }
}
