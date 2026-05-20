<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleveCompteur extends Model
{
    protected $table = 'releve_compteur';
    protected $primaryKey = 'id_releve';
    public $timestamps = false;
    protected $guarded = [];

    public function compteur()
    {
        return $this->belongsTo(Compteur::class, 'id_compteur', 'id_compteur');
    }

    // Calcul de la consommation par rapport au relevé précédent
    public function getConsommationAttribute()
    {
        $precedent = ReleveCompteur::where('id_compteur', $this->id_compteur)
            ->where('date_releve', '<', $this->date_releve)
            ->orderByDesc('date_releve')
            ->first();

        if ($precedent) {
            return $this->valeur_index - $precedent->valeur_index;
        }

        return null; // Premier relevé = pas de conso calculable
    }
}