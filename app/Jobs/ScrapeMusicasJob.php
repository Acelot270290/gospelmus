<?php

namespace App\Jobs;

use App\Services\ScrapingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeMusicasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    /**
     * Cria uma nova instância do Job.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Executa o Job.
     *
     * @return void
     */
    public function handle(ScrapingService $scrapingService)
{
    try {
        $scrapingService->scrapeMusicas($this->url);
        Log::info("Raspagem de músicas para a URL {$this->url} concluída com sucesso.");
    } catch (\Exception $e) {
        // Log da falha
        Log::error("Erro ao processar o job ScrapeMusicasJob: " . $e->getMessage());
        Log::error("Stack Trace: " . $e->getTraceAsString());

        // Rethrow para registrar como falha
        throw $e;
    }
}

}
