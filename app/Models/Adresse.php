<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'Adresse'; // Attention à la majuscule de votre SQL
    protected $primaryKey = 'id_adresse';
    public $timestamps = false;
    protected $guarded = [];

    // Une adresse appartient à un lieu-dit
    public function lieuDit()
    {
        return $this->belongsTo(LieuDit::class, 'id_lieu_dit', 'id_lieu_dit');
    }
}