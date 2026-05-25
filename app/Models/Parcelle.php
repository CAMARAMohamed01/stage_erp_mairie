<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcelle extends Model
{
    use HasFactory;

    protected $table = 'parcelle';
    protected $primaryKey = 'id_parcelle';
    public $timestamps = false;
    protected $guarded = [];

    public function lieuDit()
    {
        return $this->belongsTo(LieuDit::class, 'id_lieu_dit', 'id_lieu_dit');
    }

    // Une parcelle peut contenir plusieurs bâtiments
    public function batiments()
    {
        return $this->hasMany(Batiment::class, 'id_parcelle', 'id_parcelle');
    }
}