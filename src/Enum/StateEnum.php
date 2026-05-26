<?php

namespace App\Enum;

enum StateEnum: string
{
    case OPENED = 'Ouverte';
    case CREATED = 'Créée';
    case IN_PROGRESS = 'En cours';
    case CLOSED = 'Fermée';
    case CANCELLED = 'Annulée';
    case ENDED = 'Terminée';
    case ARCHIVED = 'Archivée';

}

