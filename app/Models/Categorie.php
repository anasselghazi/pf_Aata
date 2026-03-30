<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
class Categorie extends Model
{
   use HasFactory;
   
     protected $fillable = [
    'libelle', 
    'description'
   ];

   
   public function campagnes()
   {
    return $this->hasMany(Campagne::class);
   }
}
