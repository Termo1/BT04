<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'note_category';
    
    protected $fillable = ['name', 'color'];

  
}
