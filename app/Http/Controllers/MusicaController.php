<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MusicaService;

class MusicaController extends Controller
{
    protected $musicaService;

    public function __construct(MusicaService $musicaService)
    {
        $this->musicaService = $musicaService;
    }

    /**
     * Salvar uma música via URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salvarMusica(Request $request)
    {
        $url = $request->input('url');

        if (!$url) {
            return response()->json(['error' => 'URL não fornecida'], 400);
        }

        $result = $this->musicaService->salvarMusica($url);

        if ($result['success']) {
            return response()->json(['success' => $result['message']]);
        }

        return response()->json(['error' => $result['message']], 500);
    }
}
