<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('customer_email');
            $table->string('lineitem_name');
            $table->string('lineitem_sku');
            $table->string('lineitem_options')->nullable();
            $table->string('lineitem_addons')->nullable();
            $table->integer('lineitem_qty');
            $table->decimal('lineitem_price');
            $table->string('lineitem_type');

            $table->index('order_id');
            $table->index('lineitem_sku');
            $table->index('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
