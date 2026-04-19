<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('device_reference_id')->nullable()->after('user_id');
            $table->string('session_id')->nullable()->after('device_id');
        });

        DB::table('carts')
            ->select('id', 'device_id')
            ->orderBy('id')
            ->chunkById(100, function ($carts): void {
                foreach ($carts as $cart) {
                    if (empty($cart->device_id)) {
                        continue;
                    }

                    $deviceId = is_numeric($cart->device_id)
                        ? (int) $cart->device_id
                        : DB::table('devices')->where('user_agent', $cart->device_id)->value('id');

                    if (! $deviceId) {
                        $deviceId = DB::table('devices')->insertGetId([
                            'name' => 'Migrated Device',
                            'model' => substr((string) $cart->device_id, 0, 255),
                            'user_agent' => $cart->device_id,
                            'ip_address' => null,
                            'status' => 'active',
                            'last_seen' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('carts')
                        ->where('id', $cart->id)
                        ->update(['device_reference_id' => $deviceId]);
                }
            });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->renameColumn('device_reference_id', 'device_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('devices')->nullOnDelete();
            $table->index('session_id', 'carts_session_id_idx');
            $table->index(['user_id', 'session_id'], 'carts_user_session_idx');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropIndex('carts_session_id_idx');
            $table->dropIndex('carts_user_session_idx');
            $table->text('legacy_device_id')->nullable()->after('user_id');
        });

        DB::table('carts')
            ->select('id', 'device_id')
            ->orderBy('id')
            ->chunkById(100, function ($carts): void {
                foreach ($carts as $cart) {
                    $userAgent = DB::table('devices')->where('id', $cart->device_id)->value('user_agent');

                    DB::table('carts')
                        ->where('id', $cart->id)
                        ->update(['legacy_device_id' => $userAgent]);
                }
            });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->renameColumn('legacy_device_id', 'device_id');
            $table->dropColumn('session_id');
        });
    }
};
