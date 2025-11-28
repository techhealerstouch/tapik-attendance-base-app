<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        /**
         * 1. food_services
         */
        Schema::create('food_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        /**
         * 2. event_food_services
         */
        Schema::create('event_food_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_service_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->time('serving_start')->nullable();
            $table->time('serving_end')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'food_service_id']);
        });

        /**
         * 3. food_service_claims
         */
        Schema::create('food_service_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_service_id')->constrained()->onDelete('cascade');
            $table->timestamp('claimed_at');
            $table->foreignId('claimed_by')->nullable()->constrained('users');
            $table->string('claim_method')->default('manual'); // qr, nfc, manual
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'event_id', 'food_service_id'], 'unique_claim');
            $table->index(['event_id', 'food_service_id']);
            $table->index('claimed_at');
        });

    

        /**
         * 5. event_user
         */
        Schema::create('event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->string('status')->default('registered');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_user');
        Schema::dropIfExists('food_service_claims');
        Schema::dropIfExists('event_food_services');
        Schema::dropIfExists('food_services');
    }
};
