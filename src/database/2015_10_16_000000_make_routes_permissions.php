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

        if (! Schema::hasTable('routes')){
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()	{
        Schema::dropIfExists('routes');
    }
}
