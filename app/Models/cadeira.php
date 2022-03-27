<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $codigo
 * @property integer $ano
 * @property integer $semestre
 * @property string $tipo
 * @property string $nome
 * @property string $abreviatura
 * @property int $idCurso
 * @property string $anoletivo
 * @property Curso $curso
 * @property Inscricaouc[] $inscricaoucs
 * @property Pedidosuc[] $pedidosucs
 * @property Turno[] $turnos
 */
class cadeira extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cadeira';

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'ano', 'semestre', 'tipo', 'nome', 'abreviatura', 'idCurso', 'anoletivo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(curso::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaoucs()
    {
        return $this->hasMany(inscricaoucs::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidosucs()
    {
        return $this->hasMany(~pedidosucs::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(turno::class, 'idCadeira');
    }
}
