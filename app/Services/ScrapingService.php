<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Artista;
use App\Models\Musica;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Services\MusicaService;

class ScrapingService
{
    protected $musicaService;

    public function __construct(MusicaService $musicaService)
    {
        $this->musicaService = $musicaService;
    }

    /**
     * Processa a raspagem de músicas de um artista a partir de uma URL.
     *
     * @param string $url
     * @return array
     */
    public function scrapeMusicas(string $url): array
    {
        try {
            $client = new Client();
            $response = $client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Obter informações do artista
            $nomeArtista = $crawler->filter('.t1')->text(); // Classe que contém o nome do artista
            $imagemArtista = $crawler->filter('.art_thumb')->attr('src'); // Obter o link da imagem do artista

            // Salvar o artista e sua imagem
            $artista = $this->saveArtista($nomeArtista, $imagemArtista);

            // Obter e salvar músicas
            //$this->saveMusicas($crawler, $artista);
            $this->saveMusicas($crawler, $artista);

            return ['success' => true, 'message' => 'Músicas e artista raspados com sucesso!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Salva o artista e sua imagem.
     *
     * @param string $nomeArtista
     * @param string $imagemArtista
     * @return Artista
     */
    private function saveArtista(string $nomeArtista, string $imagemArtista): Artista
    {
        // Criar o slug do artista
        $artistaSlug = Str::slug($nomeArtista);

        // Criar a estrutura de pastas
        $pastaDestino = "artista/{$artistaSlug}/thumb";
        $nomeArquivo = "foto-" . hash('md5', $nomeArtista) . ".jpg";

        // Baixar e salvar a imagem localmente
        $conteudoImagem = file_get_contents($imagemArtista);
        Storage::disk('public')->put("{$pastaDestino}/{$nomeArquivo}", $conteudoImagem);

        // Salvar o artista no banco de dados
        $caminhoImagem = "{$pastaDestino}/{$nomeArquivo}";
        return Artista::updateOrCreate(
            ['nome' => $nomeArtista],
            ['imagem' => $caminhoImagem]
        );
    }

    /**
     * Salva as músicas do artista.
     *
     * @param Crawler $crawler
     * @param Artista $artista
     * @return void
     */
    private function saveMusicas(Crawler $crawler, Artista $artista): void
    {
        // Nessa função, podemos adicionar um array para segurar todos os links ou podemos sair disparando o job com o redis, por exemplo
        $crawler->filter('#js-a-songs > li > a')->each(function (Crawler $node) use ($artista) {
            //$tituloMusica = $node->text(); // Nome da música
            $linkMusica = $node->attr('href'); // URL da música

            // Verificar se o link é relativo e completá-lo se necessário
            if (!Str::startsWith($linkMusica, 'http')) {
                $linkMusica = 'https://www.cifraclub.com.br' . $linkMusica;
            }

            // Salvar a música no banco de dados
            $this->musicaService->salvarMusica($linkMusica);
        });
    }

    /**
     * Salva as músicas do artista.
     *
     * @param Crawler $crawler
     * @param Artista $artista
     * @return void
     */
    private function saveMusicas_old(Crawler $crawler, Artista $artista): void
    {
        $crawler->filter('.g-1 > li > a')->each(function (Crawler $node) use ($artista) {
            $tituloMusica = $node->text(); // Nome da música
            $linkMusica = $node->attr('href'); // URL da música

            // Verificar se o link é relativo e completá-lo se necessário
            if (!Str::startsWith($linkMusica, 'http')) {
                $linkMusica = 'https://www.cifraclub.com.br' . $linkMusica;
            }

            // Salvar a música no banco de dados
            Musica::updateOrCreate(
                ['artista_id' => $artista->id, 'titulo' => $tituloMusica],
                ['dados' => json_encode(['url' => $linkMusica])]
            );
        });
    }
}
