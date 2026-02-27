<?php

namespace App\Models;

use App\Enums\TermStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_en',
        'status',
        'current_version_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TermStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TermVersion::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(TermVersion::class, 'current_version_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_term');
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function editorialLogs(): HasMany
    {
        return $this->hasMany(EditorialLog::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

}
