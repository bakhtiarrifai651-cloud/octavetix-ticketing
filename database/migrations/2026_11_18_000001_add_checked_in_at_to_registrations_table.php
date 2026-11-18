<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('registrations') && ! Schema::hasColumn('registrations', 'checked_in_at')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->timestamp('checked_in_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('registrations') && Schema::hasColumn('registrations', 'checked_in_at')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropColumn('checked_in_at');
            });
        }
    }
};
