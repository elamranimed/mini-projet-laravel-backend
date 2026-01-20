<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapServerController;

Route::get('/', function () {
    return redirect('/books-app');
});

Route::get('/books-app', function () {
    //return file_get_contents(public_path('books-app.html'));
    return view('books-app');
});

Route::post('/soap', [SoapServerController::class, 'handle']);
Route::get('/soap/wsdl', [SoapServerController::class, 'wsdl']);
