<?php

namespace App\Models;

use App\Enums\TermRelationType;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'related_term_id',
        'relation_type',
    ];

    protected function casts(): array
    {
        return [
            'relation_type' => TermRelationType::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $relation): void {
            if ((int) $relation->term_id === (int) $relation->related_term_id) {
                throw new InvalidArgumentException('A term cannot be related to itself.');
            }
        });
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function relatedTerm(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'related_term_id');
    }
}
