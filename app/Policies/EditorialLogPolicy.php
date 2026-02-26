<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EditorialLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class EditorialLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EditorialLog');
    }

    public function view(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('View:EditorialLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EditorialLog');
    }

    public function update(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('Update:EditorialLog');
    }

    public function delete(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('Delete:EditorialLog');
    }

    public function restore(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('Restore:EditorialLog');
    }

    public function forceDelete(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('ForceDelete:EditorialLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EditorialLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EditorialLog');
    }

    public function replicate(AuthUser $authUser, EditorialLog $editorialLog): bool
    {
        return $authUser->can('Replicate:EditorialLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EditorialLog');
    }

}