<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnveloppeBudgetaire extends Model
{
    protected $table = 'enveloppe_budgetaire';
    protected $primaryKey = 'id_budget';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Les articles comptables liés à cette enveloppe (Table pivot)
     */
    public function articles()
    {
        return $this->belongsToMany(ArticleCompta::class, 'article_budget', 'id_budget', 'id_article');
    }
}