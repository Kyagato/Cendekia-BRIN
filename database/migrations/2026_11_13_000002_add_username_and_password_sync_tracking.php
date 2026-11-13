<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
        });
        Schema::table('keycloak_user_syncs', function (Blueprint $table) {
            $table->string('operation')->default('identity');
            $table->boolean('remote_confirmed')->default(false);
            $table->text('local_password_hash')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('keycloak_user_syncs', function (Blueprint $table) {
            $table->dropColumn(['operation', 'remote_confirmed', 'local_password_hash']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
