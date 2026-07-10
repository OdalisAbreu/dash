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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('service')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('category', ['facturado', 'perdido', 'pipeline']);
            $table->string('status')->nullable();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->boolean('pending_invoice')->default(false);
            $table->timestamps();

            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
