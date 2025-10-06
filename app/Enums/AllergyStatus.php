<?php

namespace App\Enums;

enum AllergyStatus : int
{
    //
    case UNKNOWN = 0;
    case NOT_ALLERGIC = 1;
    case ALLERGIC = 2;
}
