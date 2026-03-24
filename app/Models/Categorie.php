<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
     protected $fillable = [
    'libelle', 
    'description'
   ];

   
   public function campagnes()
   {
    return $this->hasMany(Campagne::class);
   }
}
