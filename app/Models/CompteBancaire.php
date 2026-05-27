<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteBancaire extends Model
{
    protected $table = 'compte_bancaire';

    // ⚠️ CRUCIAL : Indiquer le nom de la clé primaire
    protected $primaryKey = 'id_compte';

    // Désactiver created_at et updated_at (puisque vous utilisez date_ajout)
    public $timestamps = false;

    protected $fillable = [
        'iban',
        'rib',
        'bic',
        'titulaire_compte',
        'date_ajout',
        'id_tiers'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_compte', 'id_compte');
    }
}