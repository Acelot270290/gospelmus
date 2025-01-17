<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapeMusicasJob;
use Illuminate\Http\Request;
use App\Services\ScrapingService;

class ScrapingController extends Controller
{
    protected $scrapingService;

    public function __construct(ScrapingService $scrapingService)
    {
        $this->scrapingService = $scrapingService;
    }

    /**
     * Raspagem de músicas via URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scrapeMusicas(Request $request)
{
    $url = $request->input('url');

    if (!$url) {
        return response()->json(['error' => 'URL não fornecida'], 400);
    }

    // Disparar o Job
    ScrapeMusicasJob::dispatch($url);

    return response()->json(['success' => 'Raspagem iniciada. Você será notificado ao término.']);
}

}
