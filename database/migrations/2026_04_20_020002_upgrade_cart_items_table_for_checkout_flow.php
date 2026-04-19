<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('cart_items')
            ->select(
                'cart_id',
                'product_id',
                DB::raw('MIN(id) as keep_id'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(price) as latest_price'),
                DB::raw('COUNT(*) as row_count')
            )
            ->groupBy('cart_id', 'product_id')
            ->having('row_count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('cart_items')
                ->where('id', $duplicate->keep_id)
                ->update([
                    'quantity' => $duplicate->total_quantity,
                    'price' => $duplicate->latest_price,
                    'updated_at' => now(),
                ]);

            DB::table('cart_items')
                ->where('cart_id', $duplicate->cart_id)
                ->where('product_id', $duplicate->product_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->change();
            $table->decimal('price', 12, 2)->change();
            $table->unique(['cart_id', 'product_id'], 'cart_items_cart_product_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_product_unique');
            $table->integer('quantity')->default(1)->change();
        });
    }
};
