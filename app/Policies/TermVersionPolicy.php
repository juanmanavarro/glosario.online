<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TermVersion;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermVersionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TermVersion');
    }

    public function view(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('View:TermVersion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TermVersion');
    }

    public function update(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('Update:TermVersion');
    }

    public function delete(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('Delete:TermVersion');
    }

    public function restore(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('Restore:TermVersion');
    }

    public function forceDelete(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('ForceDelete:TermVersion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TermVersion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TermVersion');
    }

    public function replicate(AuthUser $authUser, TermVersion $termVersion): bool
    {
        return $authUser->can('Replicate:TermVersion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TermVersion');
    }

}