<?php

namespace App\Enums;

enum EditorialAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Reviewed = 'reviewed';
    case Approved = 'approved';
    case Published = 'published';
    case Archived = 'archived';
}
