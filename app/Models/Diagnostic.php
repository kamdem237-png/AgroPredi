<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    protected $table = 'diagnostics';

    protected $fillable = [
        'image_path',
        'plante',
        'maladie',
        'etat',
        'confiance',
        'niveau_risque',
        'conseils'
    ];

    protected $casts = [
        'conseils' => 'array',
        'confiance' => 'float',
    ];
}
