<?php

namespace App\Enums;

enum SenseRelationType: string
{
    case See = 'see';
    case SeeAlso = 'see_also';
    case Synonym = 'synonym';
    case Antonym = 'antonym';
    case Related = 'related';
    case Broader = 'broader';
    case Narrower = 'narrower';
}
