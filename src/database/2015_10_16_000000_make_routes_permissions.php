<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeRoutesPermissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		if ( ! Schema::hasTable('routes') ) {
			Schema::create('routes', function(Blueprint $table) {
				$table->increments('id')->unsigned();
				$table->string('verb');
				$table->string('pattern');
				$table->text('permissions');
				$table->string('resourceName')->nullable();
				$table->integer('resourceOffset')->nullable();
				$table->unique(['verb', 'pattern']);
			});
		}

		/*
		if ( ! Schema::hasTable('routes_permission') ) {
			Schema::create('routes_permission', function(Blueprint $table) {
				$table->increments('id')->unsigned();
				$table->string('name');
			});
		}

		if ( ! Schema::hasTable('routes_route_permission') ) {
			Schema::create('routes_route_permission', function(Blueprint $table) {
				$table->integer('route_id')->unsigned();
				$table->integer('permission_id')->unsigned();
				$table->foreign('route_id')->references('id')->on('routes_route')->onDelete('cascade');
				$table->foreign('permission_id')->references('id')->on('routes_permission')->onDelete('cascade');
			});
		}*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()	{
		//Schema::dropIfExists('routes_route_permission');
		//Schema::dropIfExists('routes_permission');
		Schema::dropIfExists('routes');
	}

}
