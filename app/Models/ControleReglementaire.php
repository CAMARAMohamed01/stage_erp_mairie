<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControleReglementaire extends Model
{
    use HasFactory;

    // C'est cette ligne qui corrige votre erreur :
    protected $table = 'controle_reglementaire';

    // Et n'oubliez pas la clé primaire si ce n'est pas "id"
    protected $primaryKey = 'id_controle';

    protected $guarded = [];
}