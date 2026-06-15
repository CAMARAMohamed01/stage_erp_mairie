<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    // Configuration de la table
    protected $table = 'contrat';
    protected $primaryKey = 'id_contrat';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date_signature_contrat' => 'date',
        'date_debut_contrat' => 'date',
        'date_fin_contrat' => 'date',
        'date_echeance' => 'date',
        'revision_prix_prevue' => 'boolean'
    ];

    /**
     * RELATION 1 : Le contrat appartient à un Tiers (prestataire)
     */
    public function tiers()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers');
    }

    /**
     * RELATION 2 : Un contrat génère plusieurs dossiers financiers (factures, devis)
     */
    public function dossiersFinanciers()
    {
        return $this->hasMany(DossierFinancier::class, 'id_contrat');
    }

    /**
     * RELATION 3 : Les opérations comptables liées (Table pivot operation_contrat)
     */
    public function operationsComptables()
    {
        return $this->belongsToMany(
            OperationComptable::class,
            'operation_contrat',
            'id_contrat',
            'id_operation'
        );
    }

    // Relation Many-to-Many avec les équipements couverts
    public function equipementsCouverts()
    {
        return $this->belongsToMany(Equipement::class, 'contrat_equipement', 'id_contrat', 'id_equipement')
            ->withPivot([
                'id_decision',
                'quantite_louee',
                'quantite_rendue',
                'etat_depart',
                'etat_retour',
                'montant_penalite',
                'date_debut_utilisation',
                'date_fin_utilisation',
                'statut_ligne'
            ]);
    }

    public function batimentsCouverts()
    {
        return $this->belongsToMany(\App\Models\Batiment::class, 'contrat_batiment', 'id_contrat', 'id_batiment');
    }

    public function locauxCouverts()
    {
        return $this->belongsToMany(Local::class, 'contrat_local', 'id_contrat', 'id_local')
            ->withPivot([
                'id_decision', // 🌟 Indispensable pour la suppression composite
                'date_debut_utilisation',
                'date_fin_utilisation',
                'etat_lieux_entree',
                'caution_retenue'
            ]);
    }

    public function lieuxCouverts()
    {
        return $this->belongsToMany(LieuPublic::class, 'contrat_lieu', 'id_contrat', 'id_lieu')
            ->withPivot([
                'id_decision', // 🌟 Indispensable pour la suppression composite
                'date_debut_occupation',
                'date_fin_occupation',
                'surface_occupee_m2',
                'usage_specifique',
                'etat_lieux_avant',
                'etat_lieux_apres',
                'statut_ligne'
            ]);
    }
    // Un contrat de maintenance peut couvrir plusieurs interventions (pannes, révisions)
    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'id_contrat', 'id_contrat');
    }
}