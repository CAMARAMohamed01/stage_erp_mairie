<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;

    protected $table = 'equipement';
    protected $primaryKey = 'id_equipement';
    public $timestamps = false;
    protected $guarded = [];

    // Un équipement a souvent une catégorie ou une famille
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_cat', 'id_cat');
    }

    // Selon votre modélisation, un équipement peut être lié à une adresse ou un lieu public
    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }
    public function interventions()
    {
        return $this->belongsToMany(Intervention::class, 'intervention_equipement', 'id_equipement', 'id_int');
    }

    public function famille()
    {
        return $this->belongsTo(FamilleEquipement::class, 'id_famille');
    }

    // Relation avec la table pivot soumis_a_controle
    public function controles()
    {
        return $this->belongsToMany(
            ControleReglementaire::class,
            'soumis_a_controle',
            'id_equipement',
            'id_controle'
        )->withPivot('date_controle'); // <-- On demande à Laravel de charger ce champ !
    }

    // Relation avec le Local
    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local', 'id_local');
    }
    // documents
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_equipement', 'id_equipement');
    }

    //Immobilisation
    public function immobilisation()
    {
        return $this->belongsTo(ImmobilisationInventaire::class, 'id_immo', 'id_immo');
    }
    // Relation avec le service
    public function service()
    {
        return $this->belongsTo(ServiceMairie::class, 'id_service', 'id_service');
    }
    public function lieu()
    {
        return $this->belongsTo(LieuPublic::class, 'id_lieu', 'id_lieu');
    }
    // Relation avec le Lieu Public
    public function lieuPublic()
    {
        return $this->belongsTo(LieuPublic::class, 'id_lieu', 'id_lieu');
    }

    // Relation Many-to-Many avec les contrats administratifs (Maintenance, Assurance, etc.)
    public function contratsAdministratifs()
    {
        return $this->belongsToMany(
            Contrat::class,
            'contrat_equipement',
            'id_equipement',
            'id_contrat'
        );
    }

    public function equipementParent()
    {
        return $this->belongsTo(Equipement::class, 'id_parent', 'id_equipement');
    }

    /**
     * Relation pour obtenir la liste des sous-composants rattachés
     */
    public function sousEquipements()
    {
        return $this->hasMany(Equipement::class, 'id_parent', 'id_equipement');
    }
    // Relation avec les tronçons (si un équipement peut être lié à plusieurs tronçons)
    public function troncons()
    {
        return $this->belongsTo(
            Troncon::class,
            'id_troncon',
            'id_troncon'
        );
    }
}