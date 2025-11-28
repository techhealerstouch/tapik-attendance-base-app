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
        // Create pages table
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->text('terms')->nullable();
            $table->text('privacy')->nullable();
            $table->text('contact')->nullable();
            $table->text('home_message')->nullable();
            $table->text('register')->nullable();
            $table->timestamps();
        });

        // Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('littlelink_name')->unique()->nullable();
            $table->text('littlelink_description')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('website')->nullable();
            $table->enum('role', ['user', 'vip', 'admin'])->default('user');
            $table->enum('block', ['yes', 'no'])->default('no');
            $table->string('activate_code', 191)->nullable();
            $table->string('activate_status', 191)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('theme')->nullable();
            $table->unsignedInteger('auth_as')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('rfid_no')->nullable();
            $table->integer('qr_code_status')->default(0);
            $table->boolean('new_counter')->default(true);
        });

        // Create password_resets table
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Create failed_jobs table
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Create groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create events table
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->nullable();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->boolean('status')->default(true);
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Create tickets table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('ticket_code')->unique();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('status')->nullable();
            $table->timestamps();
            
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        // Create buttons table
        Schema::create('buttons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alt')->nullable();
            $table->boolean('exclude')->default(false);
            $table->string('group')->nullable();
            $table->boolean('mb')->default(false);
            $table->timestamps();
        });

        // Create links table
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->text('link')->nullable();
            $table->text('title')->nullable();
            $table->integer('order')->default(0);
            $table->integer('click_number')->default(0);
            $table->enum('up_link', ['yes', 'no'])->default('no');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('button_id')->nullable();
            $table->timestamps();
            $table->string('custom_css')->default('');
            $table->string('custom_icon')->default('fa-external-link');
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('button_id')->references('id')->on('buttons');
        });

        // Create link_types table
        Schema::create('link_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('typename', 100);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('icon');
            $table->mediumText('params')->nullable();
            $table->boolean('active')->default(true);
        });

        // Create discounts table
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('amount', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['percentage', 'fixed']);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });

        // Create invoices table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ticket_id');
            $table->string('invoice_no')->unique();
            $table->string('xendit_id')->nullable();
            $table->string('xendit_invoice_no')->nullable();
            $table->string('invoice_url')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'FAILED', 'EXPIRED']);
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->string('currency')->default('PHP');
            $table->string('payer_email')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_destination')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->json('attendees')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });

        // Create invoice_logs table
        Schema::create('invoice_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        // Create ticket_guests table
        Schema::create('ticket_guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('ticket_no');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->boolean('is_scanned')->default(false);
            $table->timestamps();
        });

        // Create attendances table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->bigInteger('group_id')->nullable();
            $table->string('event_name')->nullable();
            $table->timestamp('time_in')->nullable();
            $table->timestamp('time_out')->nullable();
            $table->integer('late_minutes')->default(0)->nullable();
            $table->integer('early_minutes')->default(0)->nullable();
            $table->string('rep_by')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        // Create group_users table
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create professional_information table
        Schema::create('professional_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
            $table->string('work_address')->nullable();
        });

        // Create social_accounts table
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->unique()->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create visits table
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('primary_key');
            $table->string('secondary_key')->nullable();
            $table->unsignedBigInteger('score');
            $table->longText('list')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            
            $table->unique(['primary_key', 'secondary_key']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('professional_information');
        Schema::dropIfExists('group_users');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('ticket_guests');
        Schema::dropIfExists('invoice_logs');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('link_types');
        Schema::dropIfExists('links');
        Schema::dropIfExists('buttons');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('events');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
        Schema::dropIfExists('pages');
    }
};