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
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'last_login_ip')) {
                $table->string('last_login_ip', 64)->nullable()->after('active');
            }
            if (!Schema::hasColumn('accounts', 'last_login_device')) {
                $table->string('last_login_device', 255)->nullable()->after('last_login_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'last_login_device')) {
                $table->dropColumn('last_login_device');
            }
            if (Schema::hasColumn('accounts', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
        });
    }
};
