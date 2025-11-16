<?php

use App\Models\BlastJob;
use Illuminate\Support\Facades\Route;

Route::get('/master', function () {
    $data = BlastJob::all();

    return response()->json([
        'success' => true,
        'count'   => $data->count(),
        'data'    => $data,
    ]);
});
