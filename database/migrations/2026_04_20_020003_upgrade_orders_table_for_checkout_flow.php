<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->after('order_number')->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->after('device_id');
            $table->string('full_name')->nullable()->after('name');
            $table->string('email')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('area')->nullable()->after('city');
            $table->string('payment_method')->default('cod')->after('note');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('payment_status')->default('unpaid')->after('transaction_id');
            $table->string('order_status')->default('pending')->after('payment_status');
            $table->unique('order_number', 'orders_order_number_unique_idx');
            $table->index('session_id', 'orders_session_id_idx');
            $table->index(['order_status', 'payment_status'], 'orders_status_payment_idx');
        });

        DB::table('orders')
            ->select('id', 'name', 'status', 'is_paid', 'user_agent')
            ->orderBy('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    $deviceId = null;

                    if ($order->user_agent) {
                        $deviceId = DB::table('devices')->where('user_agent', $order->user_agent)->value('id');

                        if (! $deviceId) {
                            $deviceId = DB::table('devices')->insertGetId([
                                'name' => 'Migrated Order Device',
                                'model' => substr((string) $order->user_agent, 0, 255),
                                'user_agent' => $order->user_agent,
                                'ip_address' => null,
                                'status' => 'active',
                                'last_seen' => now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'order_number' => 'GL-LEGACY-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                            'full_name' => $order->name,
                            'device_id' => $deviceId,
                            'payment_status' => $order->is_paid ? 'paid' : 'unpaid',
                            'order_status' => $order->status ?: 'pending',
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['device_id']);
            $table->dropUnique('orders_order_number_unique_idx');
            $table->dropIndex('orders_session_id_idx');
            $table->dropIndex('orders_status_payment_idx');
            $table->dropColumn([
                'order_number',
                'user_id',
                'device_id',
                'session_id',
                'full_name',
                'email',
                'city',
                'area',
                'payment_method',
                'transaction_id',
                'payment_status',
                'order_status',
            ]);
        });
    }
};
