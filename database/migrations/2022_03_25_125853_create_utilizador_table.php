<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilizadorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('utilizador', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('numero')->unique('numero_UNIQUE');
			$table->string('email', 70);
			$table->string('nome', 100);
			$table->smallInteger('tipo')->default(0);
			$table->integer('idCurso')->unsigned()->index('idCurso_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('utilizador');
	}

}
