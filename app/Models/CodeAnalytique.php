<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeAnalytique extends Model
{
    //
    protected $table = 'code_analytique';
    protected $primaryKey = 'id_code';
    public $timestamps = false;
    protected $fillable = [
        'libelle_analytique',
    ];
}