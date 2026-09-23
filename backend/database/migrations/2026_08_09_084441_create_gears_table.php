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
        Schema::create('gears', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $table->string("title", 150);
            $table->text("description")->nullable();
            $table->enum("category", ["hand_tool", "cordless", "corded", "machine"]);
            $table->json("attributes")->nullable();
            $table->decimal("price_per_day", 8, 2);
            $table->string("city", 100)->index();
            $table->string("address", 255);
            $table->string("status", 20)->default("draft");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gears');
    }
};
