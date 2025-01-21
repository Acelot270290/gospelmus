<?php

namespace App\Services;

use GuzzleHttp\Client;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Artista;
use App\Models\Musica;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Services\MusicaService;
use Illuminate\Support\Facades\Log;

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

        // Cria a slug do artista
        $artistaSlug = Str::slug($nomeArtista);

        // Seta o nome do arquivo para o nome do artista (slugfied) com a extensão webp
        $nomeArquivoWebp = "{$artistaSlug}.webp";

        // Define os caminhos da imagem
        //$caminhoImagem = "{$pastaDestino}/{$nomeArquivo}";
        $caminhoImagemWebp = "{$pastaDestino}/{$nomeArquivoWebp}";

        // Baixar e salvar a imagem localmente
        $conteudoImagem = file_get_contents($imagemArtista);
        //Storage::disk('public')->put($caminhoImagem, $conteudoImagem);

        // Converter a imagem para webp com qualidade 80 usando o pacote intervention/image
        $conteudoImagemWebp = Image::make($conteudoImagem)->encode('webp', 80);
        
        // Salva a imagem no disco
        Storage::disk('public')->put($caminhoImagemWebp, $conteudoImagemWebp);

        return Artista::updateOrCreate(
            ['nome' => $nomeArtista],
            ['imagem' => $conteudoImagemWebp]
        );
    }

    /**
     * Salva as músicas do artista.
     *
     * @param Crawler $crawler
     * @param Artista $artista
     * @return void
     */
    /**
     * Salva as músicas do artista e registra logs.
     *
     * @param Crawler $crawler
     * @param Artista $artista
     * @return void
     */
    private function saveMusicas(Crawler $crawler, Artista $artista): void
    {
        // Array para armazenar os detalhes das músicas salvas
        $musicasSalvas = [];

        $crawler->filter('#js-a-songs > li > a')->each(function (Crawler $node) use ($artista, &$musicasSalvas) {
            $tituloMusica = $node->text(); // Nome da música
            $linkMusica = $node->attr('href'); // URL da música

            // Verificar se o link é relativo e completá-lo se necessário
            if (!Str::startsWith($linkMusica, 'http')) {
                $linkMusica = 'https://www.cifraclub.com.br' . $linkMusica;
            }

            // Salvar a música no banco de dados
            $this->musicaService->salvarMusica($linkMusica);

            // Adicionar música ao log
            $musicasSalvas[] = [
                'titulo' => $tituloMusica,
                'link' => $linkMusica,
            ];
        });

        // Registrar as músicas salvas no log
        if (!empty($musicasSalvas)) {
            foreach ($musicasSalvas as $musica) {
                Log::info("Música salva: {$musica['titulo']} - {$musica['link']}");
            }
        } else {
            Log::warning("Nenhuma música foi encontrada para o artista: {$artista->nome}");
        }
    }
}
