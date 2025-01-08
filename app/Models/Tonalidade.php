<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tonalidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    /**
     * Relacionamento: Uma tonalidade pode estar associada a várias músicas.
     */
    public function musicas()
    {
        return $this->hasMany(Musica::class);
    }
}
