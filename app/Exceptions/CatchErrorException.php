<?php

namespace App\Exceptions;

use Exception;

class CatchErrorException extends Exception {

    public function render() {
        return view('exception.catcherror', ['message' => $this->message]);
    }
}

