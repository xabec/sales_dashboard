<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('email');
            $table->dateTime('order_date');
            $table->string('fullfilment_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('order_status')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('fulfillment_date')->nullable();
            $table->string('currency');
            $table->decimal('subtotal');
            $table->string('shipping_method');
            $table->decimal('shipping_cost');
            $table->string('tax_method')->nullable();
            $table->decimal('taxes');
            $table->decimal('total');
            $table->string('coupon_code')->nullable();
            $table->string('coupon_code_name')->nullable();
            $table->decimal('discount')->default(0);
            $table->string('billing_name')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_address_street')->nullable();
            $table->string('billing_address_county')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_number')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_country');
            $table->string('shipping_address_street')->nullable();
            $table->string('shipping_address_county')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('gift_cards')->nullable();
            $table->string('payment_method');
            $table->string('tracking_number')->nullable();
            $table->string('special_instructions', 4096)->nullable();

            $table->index('email');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
