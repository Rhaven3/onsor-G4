<?php

namespace App\Enum;

enum StateEnum: string
{
    case OPENED = 'opened';
    case CREATED = 'created';
    case IN_PROGRESS = 'in progress';
    case CLOSED = 'closed';
    case CANCELLED = 'Annulée';
    case ENDED = 'ended';
    case ARCHIVED = 'archived';

}

