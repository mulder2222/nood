<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_pros_cons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->enum('type', ['pro', 'con']);
            $table->string('text', 500);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pros_cons');
    }
};
