<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Campagne extends Model
{ 
     use HasFactory , HasSlug;

    protected $fillable = [
    'titre',
    'slug',
    'description',
    'objectif_financier',
    'montant_collecte',      
    'statut',           
    'beneficiaire_id',
    'categorie_id',
   ];

   public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('titre')
            ->saveSlugsTo('slug');
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }


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
