<?php

namespace App\Enums;

enum PopulationStatus: int
{
    //
    case NORMAL = 1;
    case INJURED = 2;
    case DEATH = 3;
    case VULNERABLE = 4;
}
