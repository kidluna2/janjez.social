<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'reseller', 'user'])->default('user')->after('password');
            $table->decimal('balance', 15, 2)->default(0)->after('role');
            $table->string('phone')->nullable()->after('balance');
            $table->string('country')->default('Kenya')->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'balance', 'phone', 'country']);
        });
    }
};
