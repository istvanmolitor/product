<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'products',
            function (Blueprint $table) {
                $table->id();

                $table->boolean('active');

                $table->string('sku')->unique();
                $table->string('slug')->unique();

                $table->unsignedBigInteger('product_unit_id');
                $table->foreign('product_unit_id')->references('id')->on('product_units');

                $table->decimal('price', 11)->nullable();

                $table->unsignedBigInteger('currency_id');
                $table->foreign('currency_id')->references('id')->on('currencies');

                $table->softDeletes();
                $table->timestamps();
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
        Schema::dropIfExists('products');
    }
}
