<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ouvrage extends Model
{
    protected $table = 'ouvrage';
    protected $primaryKey = 'id_ouvrage';
    public $timestamps = false;
    protected $guarded = [];

    // La voie portée par cet ouvrage
    public function voie()
    {
        return $this->belongsTo(Voie::class, 'id_voie', 'id_voie');
    }
}