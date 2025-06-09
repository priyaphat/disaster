<?php

namespace App\Enums;

enum ApprovalStatus: int
{
    //
    case PENDING = 0;
    case APPROVED = 1;
    case REJECT = 2;
}
