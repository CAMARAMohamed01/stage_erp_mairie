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
    public $timestamps = false; // Désactivé si tu n'as pas created_at/updated_at dans ton SQL
    protected $guarded = [];

    // Les dates à "caster" automatiquement via Carbon pour faciliter l'affichage
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
}