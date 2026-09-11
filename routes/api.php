<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - BRANGKAS DIGITAL (LP Ma'arif NU PBNU)
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Brangkas Digital API is running'
    ]);
});

