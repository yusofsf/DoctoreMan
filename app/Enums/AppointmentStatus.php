<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case RESERVED = 'رزور شده';
    case CANCELLED = 'کنسل شده';
    case APPROVED = 'تایید شده';

    case AVAILABLE = 'قابل رزور';
}
