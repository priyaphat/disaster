<?php

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('login');


// Route::get('/mailable', function () {
//     return new WelcomeMail();
// });


Route::get('/mailable', function () {
    return new App\Mail\PreviewMail();
});