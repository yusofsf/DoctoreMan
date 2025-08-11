<?php

namespace App\Enums;

enum UserRole : string
{
    case DOCTOR = 'دکتر';
    case ADMINISTRATOR = 'ادمین';
    case PATIENT = 'بیمار';
}
