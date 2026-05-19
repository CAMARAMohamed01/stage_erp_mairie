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
    protected $fillable = [
        'code_budget',
        'date_cloture',
        'date_ouverture',
        'type_intervention',
        'statut_global',
        'description',
        'Autre',
        'id_adresse',
        'id_compteur',
        'id_troncon',
        'id_axe',
        'id_controle',
        'id_tiers',
        'id_contrat',
        'id_cat',
        'id_local',
        'id_user_demandeur',
        'id_service',
        'id_sig',
        'id_operation'
    ];

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
    public function suiviActions()
    {
        // Une intervention a plusieurs (hasMany) actions de suivi
        return $this->hasMany(SuiviAction::class, 'id_int', 'id_int')
            ->orderBy('date_action_suivi', 'desc'); // Les plus récents en premier
    }

    // la catégorie de l'intervention (ex: électricité, voirie, etc.)
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_cat', 'id_cat');
    }

    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'intervention_equipement', 'id_int', 'id_equipement');
    }

    // vers local
    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local', 'id_local');
    }
    // lieupublique
    public function lieupublic()
    {
        return $this->belongsTo(LieuPublic::class, 'id_lieu_public', 'id_lieu_public');
    }
    // contrat
    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat', 'id_contrat');
    }
    // Relation avec les agents (table utilisateur via equipe_intervention)
    public function agents()
    {
        return $this->belongsToMany(
            Utilisateur::class,
            'equipe_intervention',
            'id_int',
            'id_user'
        )->withPivot('role_agent', 'nb_heures_passees');
    }
    // tiers
    public function tiers()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }

    // Relation avec les achats de matériels et consommables
    public function achatsMateriels()
    {
        return $this->hasMany(AchatMaterielConsommable::class, 'id_int');
    }
}