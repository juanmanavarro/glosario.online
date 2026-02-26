<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'language_code',
        'title',
        'definition',
        'notes',
        'version_number',
        'created_by',
        'reviewed_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function senses(): HasMany
    {
        return $this->hasMany(TermVersionSense::class)->orderBy('sense_number');
    }
}
