<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signalement extends Model {
    protected $fillable = [
        'bagage_id', 'user_id',
        'description', 'lieu_dernier_vu', 'statut'
    ];

    public function bagage() {
        return $this->belongsTo(Bagage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
