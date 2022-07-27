<?php
//resgata os valores da tabela
//singular e com a primeira letra maiuscula = Event
// 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'items' => 'array'
    ];

    protected $dates = ['date'];

    protected $guarded = [];

    public function user() {
        return $this->belongTo('App\Models\User'); // definindo relacao one to many com User
    }

    public function users() {
        return $this->belongsToMany('App\Models\User'); // definindo relacao many to many com User
    }
}
