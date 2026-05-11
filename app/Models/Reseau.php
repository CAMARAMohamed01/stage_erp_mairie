<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseau extends Model
{
    use HasFactory;

    protected $table = 'reseau';
    protected $primaryKey = 'id_reseau';
    protected $guarded = [];

    // Un réseau est composé de plusieurs tronçons (bouts de tuyaux/câbles)
    public function troncons()
    {
        return $this->hasMany(Troncon::class, 'id_reseau', 'id_reseau');
    }
}