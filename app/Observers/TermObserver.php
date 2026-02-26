<?php

namespace App\Observers;

use App\Enums\EditorialAction;
use App\Models\EditorialLog;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;

class TermObserver
{
    public function created(Term $term): void
    {
        $this->log($term, EditorialAction::Created, 'Term created');
    }

    public function updated(Term $term): void
    {
        if (! $term->wasChanged()) {
            return;
        }

        $action = match (true) {
            $term->wasChanged('status') && $term->status?->value === 'archived' => EditorialAction::Archived,
            $term->wasChanged('status') && $term->status?->value === 'published' => EditorialAction::Published,
            default => EditorialAction::Updated,
        };

        $this->log($term, $action, 'Term updated');
    }

    private function log(Term $term, EditorialAction $action, ?string $comment = null): void
    {
        EditorialLog::create([
            'term_id' => $term->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'comment' => $comment,
        ]);
    }
}
