<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnostic extends Model
{
    protected $table = 'diagnostics';

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
