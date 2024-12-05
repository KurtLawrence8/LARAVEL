<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the 'admins' table first
        Schema::create('admins', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birthday');
            $table->string('sex');
            $table->string('company_name');
            $table->string('address');
            $table->char('contact_number');
            $table->string('type');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Now create the 'users' table
        Schema::create('users', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birthday');
            $table->string('sex');
            $table->string('address');
            $table->char('contact_number');
            $table->string('type');
            $table->string('email')->unique();
            $table->string('password');
            $table->smallInteger('admin_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('restrict');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
