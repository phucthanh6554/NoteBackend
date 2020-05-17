<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;
    protected $table = 'notes';
    protected $fillable = ['title', 'content', 'notebook_id'];

    public function notebook()
    {
        return $this->belongsTo('App\Notebook');
    }
}
