<?php

use App\Console\Commands\ScrapeMusicas;
use App\Http\Controllers\MusicaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/scrape-musicas', [ScrapeMusicas::class, 'scrapeMusicas']);
Route::post('/salvar-musica', [MusicaController::class, 'salvarMusica']);
