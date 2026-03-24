<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campagne extends Model
{
    protected $fillable = [
    'titre',
    'description',
    'objectif_financier',
    'montant_collecte',      
    'statut',           
    'beneficiaire_id',
    'categorie_id',
   ];


   public function beneficiaire()
   {
    return $this->belongsTo(User::class, 'beneficiaire_id');
   }


   public function categorie()
   {
    return $this->belongsTo(Categorie::class);
   }


   public function dons()
   {
    return $this->hasMany(Don::class);
   }


   public function favoris()
   {
    return $this->belongsToMany(User::class, 'favoris', 'campagne_id', 'donateur_id');
   }

}
