<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFieldTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'product_field_translations',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id')->references('id')->on('languages');

                $table->unsignedBigInteger('product_field_id');
                $table->foreign('product_field_id')->references('id')->on('product_fields');

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
        Schema::dropIfExists('product_field_translations');
    }
}
