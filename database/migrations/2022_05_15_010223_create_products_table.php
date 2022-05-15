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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key', 10)->unique();
            $table->string('product_title', 100);
            $table->longText('product_description');
            $table->string('style', 20);
            $table->string('available_sizes', 100);
            $table->string('brand_logo_image', 100);
            $table->string('thumbnail_image', 20);
            $table->string('color_swatch_image', 20);
            $table->string('product_image', 20);
            $table->longText('spec_sheet');
            $table->string('price_text', 100);
            $table->float('suggested_price');
            $table->string('category_name', 50);
            $table->string('subcategory_name', 50);
            $table->string('color_name', 50);
            $table->string('color_square_image', 50);
            $table->string('color_product_image', 100);
            $table->string('color_product_image_thumbnail', 100);
            $table->string('size', 5);
            $table->integer('qty');
            $table->float('piece_weight');
            $table->float('piece_price');
            $table->float('dozens_price');
            $table->float('case_price');
            $table->string('price_group', 5);
            $table->integer('case_size');
            $table->integer('inventory_key');
            $table->integer('size_index');
            $table->string('sanmar_mainframe_color', 20);
            $table->string('mill', 20);
            $table->string('product_status', 20);
            $table->longText('companion_styles')->nullable();
            $table->float('msrp')->default(0)->nullable();
            $table->float('map_pricing')->default(0)->nullable();
            $table->string('front_model_image_url', 100);
            $table->string('back_model_image', 100);
            $table->string('front_flat_image', 100);
            $table->string('back_flat_image', 100);
            $table->string('product_measurements', 50);
            $table->string('pms_color', 20)->nullable();
            $table->double('gtin')->default(0)->nullable();
            $table->timestamps();
        });
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
