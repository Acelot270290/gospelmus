<?php

use App\Http\Controllers\MusicaController;
use App\Http\Controllers\ScrapingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
});

Route::get('/scrape-musicas', [ScrapingController::class, 'scrapeMusicas']);
Route::post('/salvar-musica', [MusicaController::class, 'salvarMusica']);
