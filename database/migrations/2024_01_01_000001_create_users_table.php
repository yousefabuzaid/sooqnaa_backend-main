<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password_hash', 255);
            $table->enum('role', ['admin', 'merchant', 'customer'])->default('customer');
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active');
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->integer('login_attempts')->default(0);
            $table->timestamp('last_failed_login_at')->nullable();
            $table->text('avatar_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and security
            $table->index('email', 'idx_users_email');
            $table->index('phone', 'idx_users_phone');
            $table->index(['email', 'status'], 'idx_users_email_status');
            $table->index(['role', 'status'], 'idx_users_role_status');
            $table->index('created_at', 'idx_users_created_at');
            $table->index('login_attempts', 'idx_users_login_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
