<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artista extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    /**
     * Relacionamento: Um artista pode ter várias músicas.
     */
    public function musicas()
    {
        return $this->hasMany(Musica::class);
    }
}
