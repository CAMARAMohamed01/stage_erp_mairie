<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LieuPublic extends Model
{
    use HasFactory;

    protected $table = 'lieux_publics';
    protected $primaryKey = 'id_lieu';
    protected $guarded = [];

    // Relation inverse
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_lieu', 'id_lieu', 'id_projet');
    }
}