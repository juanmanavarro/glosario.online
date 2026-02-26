<?php

namespace App\Models;

use App\Enums\EditorialAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'user_id',
        'action',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'action' => EditorialAction::class,
        ];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
