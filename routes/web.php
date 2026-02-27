<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
});

Route::view('/browse', 'browse')->name('browse');

Route::get('/login', fn () => redirect('/admin/login'))->name('login');

Route::middleware('auth')->get('/glosary', function (Request $request) {
    abort_unless($request->user()?->hasRole('member'), 403);

    return response('Zona privada member (/glosary)');
});
