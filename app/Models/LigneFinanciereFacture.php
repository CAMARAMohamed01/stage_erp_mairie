<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneFinanciereFacture extends Model
{
    // ⚠️ On cible le nom exact de ta table SQL (avec le sous-tiret)
    protected $table = 'ligne_financiere_facture_';
    protected $primaryKey = 'id_ligne';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date_comptable' => 'date',
    ];

    public function operationComptable()
    {
        return $this->belongsTo(OperationComptable::class, 'id_operation', 'id_operation');
    }

    public function enveloppeBudgetaire()
    {
        return $this->belongsTo(EnveloppeBudgetaire::class, 'id_budget', 'id_budget');
    }
}