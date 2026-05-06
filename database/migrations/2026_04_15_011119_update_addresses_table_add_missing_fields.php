<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'alias')) {
                $table->string('alias')->after('user_id');
            }

            if (!Schema::hasColumn('addresses', 'recipient_name')) {
                $table->string('recipient_name')->after('alias');
            }

            if (!Schema::hasColumn('addresses', 'phone')) {
                $table->string('phone')->nullable()->after('recipient_name');
            }

            if (!Schema::hasColumn('addresses', 'street')) {
                $table->string('street')->after('phone');
            }

            if (!Schema::hasColumn('addresses', 'neighborhood')) {
                $table->string('neighborhood')->nullable()->after('street');
            }

            if (!Schema::hasColumn('addresses', 'city')) {
                $table->string('city')->after('neighborhood');
            }

            if (!Schema::hasColumn('addresses', 'state')) {
                $table->string('state')->after('city');
            }

            if (!Schema::hasColumn('addresses', 'zip_code')) {
                $table->string('zip_code')->after('state');
            }

            if (!Schema::hasColumn('addresses', 'references')) {
                $table->text('references')->nullable()->after('zip_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $columns = [
                'alias',
                'recipient_name',
                'phone',
                'street',
                'neighborhood',
                'city',
                'state',
                'zip_code',
                'references',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('addresses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};