<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->length(11)->default(0);
            $table->string('warehouse')->length(20);
            $table->double('quantity',20,2)->default(0)->nullable();
            $table->double('weight',20,2)->default(0)->nullable();
            $table->double('length',20,2)->default(0)->nullable();
            $table->double('width',20,2)->default(0)->nullable();
            $table->string('heat_no')->length(20)->nullable();
            $table->string('lot_no')->length(20)->nullable();
            $table->string('sc_no')->length(20)->nullable();
            $table->string('supplier')->length(150)->nullable();
            $table->string('supplier_heat_no')->length(20)->nullable();
            $table->string('material_used')->length(10);
            $table->double('weight_received',20,2)->default(0)->nullable();
            $table->integer('is_excess')->length(1)->default(0);
            $table->unsignedInteger('create_user')->length(11)->default(0);
            $table->unsignedInteger('update_user')->length(11)->default(0);
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
        Schema::dropIfExists('inventory_items');
    }
}
