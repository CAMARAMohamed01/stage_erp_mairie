<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiers extends Model
{
    use HasFactory;

    protected $table = 'tiers';
    protected $primaryKey = 'id_tiers';
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['type_tiers', 'tel_tiers', 'email_tiers', 'id_adresse'];

    // Le Tiers possède une adresse
    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    // Liens vers ses "enfants" potentiels (Héritage)
    public function physique()
    {
        return $this->hasOne(TiersPhysique::class, 'id_tiers', 'id_tiers');
    }

    public function morale()
    {
        return $this->hasOne(TiersMorale::class, 'id_tiers', 'id_tiers');
    }
    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'id_tiers', 'id_tiers');
    }

    // Création d'un attribut virtuel "nom_affiche"
    public function getNomAfficheAttribute()
    {
        // On importe la façade DB en haut du fichier si besoin : use Illuminate\Support\Facades\DB;

        if (str_contains(strtolower($this->type_tiers), 'morale') || str_contains(strtolower($this->type_tiers), 'entreprise')) {
            $morale = \Illuminate\Support\Facades\DB::table('tiers_morale')->where('id_tiers', $this->id_tiers)->first();
            return $morale ? $morale->raison_sociale : 'Entreprise introuvable';
        } else {
            $physique = \Illuminate\Support\Facades\DB::table('tiers_physique')->where('id_tiers', $this->id_tiers)->first();
            return $physique ? $physique->nom_tiers . ' ' . $physique->prenom_tiers : 'Personne introuvable';
        }
    }
}