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
        Schema::create('contact_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('phone')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->text('profile_image')->nullable();
            $table->text('additional_document')->nullable();
            $table->boolean('is_merged')->default(false);
            $table->unsignedBigInteger('parent_contact_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_masters');
    }
};
