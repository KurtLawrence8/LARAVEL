<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_code')->unique();
            $table->string('brand');
            $table->string('name');
            $table->string('size');
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            $table->unsignedInteger('category_id');
            $table->unsignedSmallInteger('admin_id'); // Associate product with an admin
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade'); // Cascade on admin deletion
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); // Cascade on category deletion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
