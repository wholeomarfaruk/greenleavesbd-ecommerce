<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order__items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::rename('order__items', 'order_items');

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_image')->nullable()->after('product_name');
            $table->decimal('unit_price', 12, 2)->nullable()->after('product_image');
            $table->decimal('line_total', 12, 2)->nullable()->after('quantity');
            $table->decimal('price', 12, 2)->nullable()->change();
        });

        DB::table('order_items')
            ->select('id', 'product_id', 'price', 'quantity')
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $product = $item->product_id
                        ? DB::table('products')->select('name', 'image')->where('id', $item->product_id)->first()
                        : null;

                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update([
                            'product_name' => $product?->name,
                            'product_image' => $product?->image ? 'storage/images/products/thumbnails/' . $product->image : null,
                            'unit_price' => $item->price,
                            'line_total' => round(((float) $item->price) * $item->quantity, 2),
                            'updated_at' => now(),
                        ]);
                }
            });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn([
                'product_name',
                'product_image',
                'unit_price',
                'line_total',
            ]);
        });

        Schema::rename('order_items', 'order__items');

        Schema::table('order__items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};
