<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musica extends Model
{
    use HasFactory;

    protected $fillable = ['artista_id', 'titulo', 'tonalidade_id', 'dados', 'conteudo'];

    /**
     * Relacionamento: Uma música pertence a um artista.
     */
    public function artista()
    {
        return $this->belongsTo(Artista::class);
    }

    /**
     * Relacionamento: Uma música pertence a uma tonalidade.
     */
    public function tonalidade()
    {
        return $this->belongsTo(Tonalidade::class);
    }
}
