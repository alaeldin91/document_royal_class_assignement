<?php

use App\Models\DocumentHeader;
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
        Schema::create('versions', function (Blueprint $table) {
        $table->id();
        $table->foreignIdFor(DocumentHeader::class,'document_id')->onDelete('cascade')->index();
        $table->integer('version_number');
        $table->json('changes_summary')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
