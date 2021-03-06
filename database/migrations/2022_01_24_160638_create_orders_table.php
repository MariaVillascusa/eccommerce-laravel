<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('contact');
            $table->string('phone');
            $table->enum('status', [
                Order::PENDIENTE, Order::RECIBIDO, Order::ENVIADO, Order::ENTREGADO, Order::ANULADO
            ])->default(Order::PENDIENTE);
            $table->enum('envio_type', [1, 2]);
            $table->float('shipping_cost');
            $table->float('total');
            $table->json('content');
            $table->json('envio')->nullable();
            $table->foreignId('department_id')->nullable()->references('id')->on('departments');
            $table->foreignId('city_id')->nullable()->references('id')->on('cities');
            $table->foreignId('district_id')->nullable()->references('id')->on('districts');
            $table->string('address')->nullable();
            $table->string('reference')->nullable();

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
        Schema::dropIfExists('orders');
    }
}
