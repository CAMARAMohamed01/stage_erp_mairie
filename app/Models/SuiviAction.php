<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SuiviAction extends Model
{
    protected $table = 'suivi_action';
    protected $primaryKey = 'id_action';
    public $timestamps = false; // Pas de created_at/updated_at dans ton SQL

    protected $fillable = [
        'date_action_suivi',
        'cout_associe',
        'temps_passe_heures',
        'statut_apres_action',
        'description_etape',
        'id_int',
        'id_user'
    ];

    // Relation inverse : une action appartient à une intervention
    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'id_int', 'id_int');
    }
}