<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCompta extends Model
{
    protected $table = 'article_compta';
    protected $primaryKey = 'id_article';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Les chapitres associés à cet article (Table pivot)
     */
    public function chapitres()
    {
        return $this->belongsToMany(Chapitre::class, 'article_chapitre', 'id_article', 'id_chapitre');
    }
}