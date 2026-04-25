<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paystack_pending_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 120)->unique();
            $table->json('cart_payload');
            $table->json('validated_payload');
            $table->unsignedInteger('expected_amount_pesewas');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paystack_pending_checkouts');
    }
};
