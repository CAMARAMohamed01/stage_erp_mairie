<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilleEquipement extends Model
{
    // 1. Le vrai nom de la table (sans le "s" de Laravel)
    protected $table = 'famille_equipement';

    // 2. Le vrai nom de votre clé primaire
    protected $primaryKey = 'id_famille';

    // 3. Désactiver les dates automatiques (ce qui a causé votre erreur n°2)
    public $timestamps = false;

    // 4. Autoriser l'insertion de données multiples (Mass Assignment)
    protected $guarded = [];
}