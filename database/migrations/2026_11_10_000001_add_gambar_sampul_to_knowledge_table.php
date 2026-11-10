<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The API already accepts covers; persist their paths for authorized retrieval.
        if (! Schema::hasColumn('knowledge', 'gambar_sampul')) {
            Schema::table('knowledge', function (Blueprint $table) {
                $table->string('gambar_sampul')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Retain stored file references when rolling back this security change.
    }
};
