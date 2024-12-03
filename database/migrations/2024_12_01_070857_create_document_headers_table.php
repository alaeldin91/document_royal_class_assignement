<?php

use App\Models\User;
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
        Schema::create('document_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class,'user_id')->onDelete('cascade')->index();
            $table->string('module');
            $table->integer('version')->default(1);
            $table->json('metadata')->nullable();
            $table->text('encryption_key');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_headers');
    }
};
