<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFieldOptionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'product_field_option_translations',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('product_field_option_id');
                $table->foreign('product_field_option_id', 'option_foreign')->references('id')->on('product_field_options');

                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id', 'language_foreign')->references('id')->on('languages');

                $table->string('name')->nullable();
                $table->text('description')->nullable();
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
        Schema::dropIfExists('product_field_option_translations');
    }
}
