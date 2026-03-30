<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'product_translations',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id')->references('id')->on('languages');

                $table->unsignedBigInteger('product_id');
                $table->foreign('product_id')->references('id')->on('products');

                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->text('short_description')->nullable();
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
        Schema::dropIfExists('product_translations');
    }
}
