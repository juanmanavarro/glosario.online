<?php

namespace App\Enums;

enum TermRelationType: string
{
    case Synonym = 'synonym';
    case Antonym = 'antonym';
    case Related = 'related';
    case Broader = 'broader';
    case Narrower = 'narrower';
    case SeeAlso = 'see_also';
}
