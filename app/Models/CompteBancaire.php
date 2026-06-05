<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class CompteBancaire extends Model
{
    protected $table = 'compte_bancaire';

    protected $primaryKey = 'id_compte';

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
    public function setIbanAttribute($value)
    {
        $clean = str_replace(' ', '', strtoupper($value));
        $this->attributes['iban'] = Crypt::encryptString($clean);
    }

    public function setBicAttribute($value)
    {
        $clean = str_replace(' ', '', strtoupper($value));
        $this->attributes['bic'] = Crypt::encryptString($clean);
    }

    public function setRibAttribute($value)
    {
        // Si le RIB est fourni, on le nettoie et on le crypte
        if (!empty($value)) {
            $clean = str_replace(' ', '', strtoupper($value));
            $this->attributes['rib'] = Crypt::encryptString($clean);
        } else {
            $this->attributes['rib'] = null;
        }
    }

    /**
     * Déchiffrement automatique à la lecture
     */
    public function getIbanAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value; // Sécurité si données d'anciennes versions en clair
        }
    }

    public function getBicAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value;
        }
    }

    public function getRibAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value;
        }
    }

}