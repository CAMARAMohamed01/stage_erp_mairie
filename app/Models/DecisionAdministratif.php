<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionAdministratif extends Model
{
    protected $table = 'decision_administratif';
    protected $primaryKey = 'id_decision';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date_decision' => 'date',
        'teletransmission_prefecture' => 'boolean'
    ];

    /**
     * L'agent ou l'élu qui a rédigé l'acte
     */
    public function redacteur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user_redacteur', 'id_user');
    }

    /**
     * Les opérations comptables associées ou financées par cet acte
     */
    public function operationsComptables()
    {
        return $this->belongsToMany(OperationComptable::class, 'acte_operation', 'id_decision', 'id_operation');
    }
}