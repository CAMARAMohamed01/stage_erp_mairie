<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchatMaterielConsommable extends Model
{
    //
    protected $table = 'achat_materiel_consommable';
    protected $primaryKey = 'id_achat';
    protected $gaurded = [];
    public $timestamps = false;

}