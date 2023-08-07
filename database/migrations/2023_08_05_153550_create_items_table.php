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
            $table->string('item_code')->length(20);
            $table->text('item_desc');
            $table->string('item_category')->length(20); // RAW MATERIAL / PRODUCT / CRUDE
            $table->string('item_type')->length(200); // Material Type / Product Line
            $table->string('item')->length(50)->nullable(); //M-P
            $table->string('schedule_class')->length(50)->nullable(); //M-P
            $table->string('alloy')->nullable()->length(50); //M-P
            $table->string('size')->nullable()->length(50); //M-P
            $table->double('weight',20,2)->default(0.00)->nullable(); //M-P
            $table->double('cut_weight',20,2)->default(0.00)->nullable(); //P
            $table->double('cut_length',20,2)->default(0.00)->nullable(); //P
            $table->double('cut_width',20,2)->default(0.00)->nullable(); //P
            $table->string('std_material_used')->length(50)->nullable(); //P
            $table->string('finished_code')->length(50)->nullable(); //P
            $table->text('finished_desc')->nullable(); //P
            $table->integer('is_deleted')->length(1)->default(0);
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
        Schema::dropIfExists('items');
    }
}
