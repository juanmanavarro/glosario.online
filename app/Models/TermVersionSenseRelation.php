<?php

namespace App\Models;

use App\Enums\SenseRelationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermVersionSenseRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_version_sense_id',
        'related_term_id',
        'relation_type',
    ];

    protected function casts(): array
    {
        return [
            'relation_type' => SenseRelationType::class,
        ];
    }

    public function sense(): BelongsTo
    {
        return $this->belongsTo(TermVersionSense::class, 'term_version_sense_id');
    }

    public function relatedTerm(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'related_term_id');
    }
}
