<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteBancaire extends Model
{

    protected $table = 'compte_bancaire';
    protected $primaryKey = 'id_compte';
    protected $guarded = [];
    public $timestamps = false;

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_compte', 'id_compte');
    }
}