<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    use HasFactory;
    protected $fillable = [
     'montant',
     'donateur_id',
     'campagne_id',
    ];


    
    public function donateur()
     {
    return $this->belongsTo(User::class, 'donateur_id');
     }


    public function campagne()
     {
      return $this->belongsTo(Campagne::class);
     }


    
}
