<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosucsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedidosucs', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('idCadeira')->unsigned()->index('idCadeira___idx');
			$table->integer('idPedidos')->unsigned()->index('idPedidos_idx');
			$table->timestamps(10);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pedidosucs');
	}

}
