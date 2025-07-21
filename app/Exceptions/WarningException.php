<?php

namespace App\Exceptions;

use Exception;

class WarningException extends Exception
{

    public function report()
    {
        return false;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message'=> $this->message,
            'is_warning'=> true
        ], 200);
    }
}
