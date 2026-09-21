<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class MyValidationException extends Exception {

    public function __construct(Validator $validator) {
        $this->validator = $validator;
    }

    public function render() {
        // return a json with desired format
        return response()->json([
            "response" => $this->validator->errors()
        ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
