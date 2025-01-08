<?php

namespace App\Services;

use App\Models\Artista;
use App\Models\Musica;
use App\Models\Tonalidade;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class MusicaService
{
    /**
     * Processa e salva uma música a partir de uma URL.
     *
     * @param string $url
     * @return array
     */
    public function salvarMusica(string $url): array
{
    try {
        $client = new Client();
        $response = $client->get($url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Obter informações do artista
        if ($crawler->filter('.t3 > a')->count() > 0) {
            $nomeArtista = $crawler->filter('.t3 > a')->text();
            $imagemArtista = $crawler->filter('.art_thumb')->count() > 0
                ? $crawler->filter('.art_thumb')->attr('src')
                : null;

            $artista = $this->getOrCreateArtista($nomeArtista, $imagemArtista);
        } else {
            throw new \Exception('Não foi possível encontrar o nome do artista.');
        }

        // Obter informações da música
        if ($crawler->filter('h1.t1')->count() > 0) {
            $tituloMusica = $crawler->filter('h1.t1')->text();
        } else {
            throw new \Exception('Não foi possível encontrar o título da música.');
        }

        if ($crawler->filter('#cifra_tom a')->count() > 0) {
            $tonalidade = $crawler->filter('#cifra_tom a')->text();
        } else {
            throw new \Exception('Não foi possível encontrar a tonalidade da música.');
        }

        if ($crawler->filter('pre')->count() > 0) {
            $conteudo = $crawler->filter('pre')->html();
        } else {
            throw new \Exception('Não foi possível encontrar o conteúdo da música.');
        }

        // Salvar tonalidade
        $tonalidadeId = $this->getOrCreateTonalidade($tonalidade);

        // Codificar o conteúdo como JSON
        $conteudoJson = json_encode(['html' => $conteudo]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Erro ao codificar o conteúdo como JSON: ' . json_last_error_msg());
        }

        // Salvar música no banco de dados
        $musica = Musica::updateOrCreate(
            ['artista_id' => $artista->id, 'titulo' => $tituloMusica],
            [
                'tonalidade_id' => $tonalidadeId,
                'conteudo' => $conteudoJson,
            ]
        );

        return [
            'success' => true,
            'message' => "A música '{$musica->titulo}' foi salva com sucesso!",
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}



   /**
 * Verifica se o artista existe ou cria um novo com imagem.
 *
 * @param string $nomeArtista
 * @param string|null $imagemArtista
 * @return Artista
 */
private function getOrCreateArtista(string $nomeArtista, ?string $imagemArtista = null): Artista
{
    $artista = Artista::firstOrCreate(['nome' => $nomeArtista]);

    // Se a imagem for fornecida e o artista ainda não tiver uma
    if ($imagemArtista && !$artista->imagem) {
        $slugArtista = \Illuminate\Support\Str::slug($nomeArtista);
        $pastaDestino = "artista/{$slugArtista}/thumb";
        $nomeArquivo = "foto-" . hash('md5', $nomeArtista) . ".jpg";

        // Baixar e salvar a imagem localmente
        $conteudoImagem = file_get_contents($imagemArtista);
        Storage::disk('public')->put("{$pastaDestino}/{$nomeArquivo}", $conteudoImagem);

        // Atualizar o caminho da imagem no artista
        $artista->update(['imagem' => "{$pastaDestino}/{$nomeArquivo}"]);
    }

    return $artista;
}


    /**
     * Verifica se a tonalidade existe ou cria uma nova.
     *
     * @param string $tonalidade
     * @return int
     */
    private function getOrCreateTonalidade(string $tonalidade): int
    {
        $tonalidadeModel = Tonalidade::firstOrCreate(['nome' => $tonalidade]);
        return $tonalidadeModel->id;
    }
}
