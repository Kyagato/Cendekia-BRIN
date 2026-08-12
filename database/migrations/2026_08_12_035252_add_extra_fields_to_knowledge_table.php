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
        Schema::table('knowledge', function (Blueprint $table) {
            $table->string('penulis')->nullable()->after('judul');
            $table->string('kolaborator')->nullable()->after('penulis');
            $table->string('url_teks')->nullable()->after('kolaborator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->dropColumn(['penulis', 'kolaborator', 'url_teks']);
        });
    }
};
