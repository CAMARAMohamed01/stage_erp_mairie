<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionCommission extends Model
{
    protected $table = 'decision_commission';
    protected $primaryKey = 'id_decision';
    public $timestamps = false;
    protected $guarded = [];

    // Projet de mandat lié
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }

    // Agent/Secrétaire ayant enregistré le PV de commission
    public function enregistreur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_enregistreur_decision', 'id_user');
    }

    // Intervention des services techniques liée
    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'id_int', 'id_int');
    }

    // Écriture comptable associée
    public function operationComptable()
    {
        return $this->belongsTo(OperationComptable::class, 'id_operation', 'id_operation');
    }
}