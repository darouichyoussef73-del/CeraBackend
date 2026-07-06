<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service_type');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->string('participants')->nullable();
            $table->string('experience')->nullable();
            $table->string('piece_type')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('glaze_preference')->nullable();
            $table->text('message')->nullable();
            $table->boolean('newsletter')->default(false);
            $table->string('vision_image')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
