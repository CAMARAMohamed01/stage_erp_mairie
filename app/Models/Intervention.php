<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Intervention extends Model
{
    use HasFactory;

    protected $table = 'intervention';
    protected $primaryKey = 'id_int';
    public $timestamps = false;
    protected $guarded = [];

    // Une intervention peut faire suite à un signalement
    public function signalement()
    {
        return $this->belongsTo(Signalement::class, 'id_sig', 'id_sig');
    }

    // L'agent ou l'équipe assignée à l'intervention
    public function responsable()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user', 'id_user');
    }

    // L'équipement concerné par l'intervention
    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'id_equipement', 'id_equipement');
    }

    // la catégorie de l'intervention (ex: électricité, voirie, etc.)
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_cat', 'id_cat');
    }
}