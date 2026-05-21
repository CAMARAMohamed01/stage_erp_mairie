<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    // On force le nom singulier de la table
    protected $table = 'document';

    // On précise la clé primaire
    protected $primaryKey = 'id_document';

    // On désactive les timestamps (created_at, updated_at) si tu ne les as pas dans ta table
    public $timestamps = false;

    protected $guarded = [];
}