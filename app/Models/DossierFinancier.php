<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierFinancier extends Model
{
    use HasFactory;

    protected $table = 'dossier_financier';
    protected $primaryKey = 'id_dossier_f';
    public $timestamps = false;
    protected $guarded = [];

    // Cast des nombreuses dates du DDL pour faciliter les manipulations avec Carbon
    protected $casts = [
        'date_constatation_recette' => 'date',
        'date_emission_titre' => 'date',
        'date_encaissement' => 'date',
        'date_reception_devis' => 'date',
        'date_signature_engagement' => 'date',
        'date_bon_livraison' => 'date',
        'date_service_fait' => 'date',
        'date_reception_facture' => 'date',
        'date_transmission_compta' => 'date',
    ];

    /**
     * Un dossier financier peut être rattaché à un contrat cadre.
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    /**
     * Un dossier financier concerne un tiers (fournisseur / prestataire).
     */
    public function tiers()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers');
    }

    /**
     * Les lignes budgétaires ou de factures associées.
     */
    public function lignes()
    {
        return $this->hasMany(LigneFinanciereFacture::class, 'id_dossier_f', 'id_dossier_f');
    }
    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class, 'id_dossier_f', 'id_dossier_f');
    }
}