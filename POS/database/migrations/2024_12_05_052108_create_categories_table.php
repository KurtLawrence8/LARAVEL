<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedSmallInteger('admin_id'); // Foreign key to admins table
            $table->timestamps();

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade'); // Delete categories if admin is deleted
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
