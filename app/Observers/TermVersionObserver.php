<?php

namespace App\Observers;

use App\Enums\EditorialAction;
use App\Enums\TermStatus;
use App\Models\EditorialLog;
use App\Models\TermVersion;
use Illuminate\Support\Facades\Auth;

class TermVersionObserver
{
    public function creating(TermVersion $termVersion): void
    {
        if (! $termVersion->version_number) {
            $max = TermVersion::query()
                ->where('term_id', $termVersion->term_id)
                ->where('language_code', $termVersion->language_code)
                ->max('version_number');

            $termVersion->version_number = ((int) $max) + 1;
        }
    }

    public function created(TermVersion $termVersion): void
    {
        $this->log($termVersion, EditorialAction::Created, 'Term version created');

        if ($termVersion->approved_at !== null) {
            $this->publishApprovedVersion($termVersion);
            $this->log($termVersion, EditorialAction::Approved, 'Term version approved');
        }
    }

    public function updated(TermVersion $termVersion): void
    {
        if ($termVersion->wasChanged('approved_at') && $termVersion->approved_at !== null) {
            $this->publishApprovedVersion($termVersion);
            $this->log($termVersion, EditorialAction::Approved, 'Term version approved');
        }
    }

    private function publishApprovedVersion(TermVersion $termVersion): void
    {
        $term = $termVersion->term;

        if ($term === null) {
            return;
        }

        $term->current_version_id = $termVersion->id;
        $term->status = TermStatus::Published;
        $term->published_at ??= now();
        $term->save();
    }

    private function log(TermVersion $termVersion, EditorialAction $action, ?string $comment = null): void
    {
        EditorialLog::create([
            'term_id' => $termVersion->term_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'comment' => $comment,
        ]);
    }
}
