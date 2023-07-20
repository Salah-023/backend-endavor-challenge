<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'address',
        'checked',
        'description',
        'interest',
        'dateOfBirth',
        'email',
        'account',
        'creditCardId'
    ];

    public $timestamps = false;
    public function CreditCard(){
        return $this->hasOne(CreditCard::class);
    }
}
