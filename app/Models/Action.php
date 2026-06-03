<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class action extends Model
{
    use HasFactory;

    protected $table = 'action';
    protected $primaryKey = 'id_action';
    public $timestamps = false;
    protected $guarded = [];


    // L'agent qui a saisi le action
    public function agent()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user', 'id_user');
    }

    // Le citoyen (Optionnel, grâce à notre approche hybride !)
    public function tiers()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }

    // La localisation (Optionnelle selon le type de problème)
    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    // La catégorie du problème
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_cat', 'id_cat');
    }
    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local');
    }
}