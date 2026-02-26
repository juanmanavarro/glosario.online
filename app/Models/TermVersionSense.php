<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermVersionSense extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_version_id',
        'sense_number',
        'definition',
    ];

    public function termVersion(): BelongsTo
    {
        return $this->belongsTo(TermVersion::class);
    }

    public function relations(): HasMany
    {
        return $this->hasMany(TermVersionSenseRelation::class, 'term_version_sense_id');
    }
}
