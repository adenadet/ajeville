<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_products', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->text('description');
            $table->text('details')->nullable();
            $table->double('unit_price');
            $table->integer('status');
            $table->integer('quantity');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_products');
    }
};
