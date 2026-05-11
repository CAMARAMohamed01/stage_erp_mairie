<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierUrba extends Model
{
    use HasFactory;

    protected $table = 'dossier_urba';
    protected $primaryKey = 'id_dossier'; // Adaptez si besoin
    protected $guarded = [];

    // Le dossier concerne une parcelle spécifique
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'id_parcelle', 'id_parcelle');
    }

    // Le demandeur du permis est un Tiers (Citoyen ou Entreprise)
    public function demandeur()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }
}