<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Troncon extends Model
{
    use HasFactory;

    protected $table = 'troncon';
    protected $primaryKey = 'id_troncon';
    protected $guarded = [];

    // Le tronçon appartient à un réseau global
    public function reseau()
    {
        return $this->belongsTo(Reseau::class, 'id_reseau', 'id_reseau');
    }
}