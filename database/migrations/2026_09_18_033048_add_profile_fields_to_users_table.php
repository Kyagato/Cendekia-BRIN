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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'no_telepon')) {
                $table->string('no_telepon', 30)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'pekerjaan')) {
                $table->string('pekerjaan', 100)->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('jenis_kelamin');
            }
            $table->string('jenis_kelamin', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(['no_telepon', 'pekerjaan', 'alamat'], fn($col) => Schema::hasColumn('users', $col)));
        });
    }
};
