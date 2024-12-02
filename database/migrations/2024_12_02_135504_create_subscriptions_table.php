<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user
            $table->foreignId('subscription_category_id')->constrained()->onDelete('cascade'); // Reference to subscription category
            $table->boolean('paid')->default(false); // Whether payment was made
            $table->string('payment_transaction')->nullable(); // Transaction ID or reference
            $table->timestamp('start_time')->nullable(); // When the subscription starts
            $table->timestamp('end_time')->nullable(); // When the subscription expires
            $table->timestamps();
        });
    }

/**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
