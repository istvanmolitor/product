<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUnitTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'product_unit_translations',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id')->references('id')->on('languages');

                $table->unsignedBigInteger('product_unit_id');
                $table->foreign('product_unit_id')->references('id')->on('product_units');

                $table->string('name')->nullable();
                $table->string('short_name')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_unit_translations');
    }
}
