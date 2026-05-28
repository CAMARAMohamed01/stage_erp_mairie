<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierUrba extends Model
{
    use HasFactory;

    protected $table = 'dossier_urba';
    protected $primaryKey = 'id_dossier';
    public $timestamps = false;

    protected $fillable = [
        'numero_dossier',
        'type_dossier_CU_DP_', // PC, DP, CU...
        'date_depot',
        'date_decision',
        'nature_decision',     // Accordé, Refusé, En cours...
        'objet_travaux',
        'surface_plancher_m2',
        'hauteur_construction',
        'prix_m2_ia',
        'date_limite_instruction',
        'avis_maire',
        'observations',
        'id_tiers',
        'id_acte_decision',
        'id_user_instructeur'
    ];

    protected $casts = [
        'date_depot' => 'date',
        'date_decision' => 'date',
        'date_limite_instruction' => 'date',
        'surface_plancher_m2' => 'decimal:2',
        'hauteur_construction' => 'decimal:2'
    ];

    // Le demandeur (Tiers physique ou morale)
    public function demandeur()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }

    // L'agent municipal chargé du dossier
    public function instructeur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user_instructeur', 'id_user');
    }

    // L'arrêté municipal officiel lié à la décision
    public function acteDecision()
    {
        return $this->belongsTo(DecisionAdministratif::class, 'id_acte_decision', 'id_decision');
    }

    // Les parcelles concernées par les travaux (Many-to-Many via pivot)
    public function parcelles()
    {
        return $this->belongsToMany(Parcelle::class, 'dossier_parcelle', 'id_dossier', 'id_parcelle');
    }

    // Les pièces jointes numérisées (Plans, CERFA, photos)
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_dossier', 'id_dossier');
    }
}