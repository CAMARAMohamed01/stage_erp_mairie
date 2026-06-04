<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleReglementaire extends Model
{
    protected $table = 'controle_reglementaire';
    protected $primaryKey = 'id_controle';
    public $timestamps = false;

    protected $fillable = [
        'designation',
        'domaine_technique',
        'est_legalement_obligatoire',
        'frequence_mois',
        'type_controle',
        'type_document_attendu',
        'intervenant_prevu'
    ];

    // La relation Many-to-Many vers les types ERP
    public function typesErp()
    {
        return $this->belongsToMany(TypeErp::class, 'type_erp_controle', 'id_controle', 'id_type_erp')
            ->withPivot('date_controle');
    }
    // La relation Many-to-Many vers les Équipements
    public function equipements()
    {
        return $this->belongsToMany(
            Equipement::class,
            'soumis_a_controle',
            'id_controle',       // Clé étrangère du modèle actuel
            'id_equipement'      // Clé étrangère du modèle cible
        )->withPivot('date_controle'); // On récupère la date du dernier contrôle
    }

}