<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapitre extends Model
{
    protected $table = 'chapitre';
    protected $primaryKey = 'id_chapitre';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Les articles contenus dans ce chapitre
     */
    public function articles()
    {
        return $this->belongsToMany(ArticleCompta::class, 'article_chapitre', 'id_chapitre', 'id_article');
    }
}