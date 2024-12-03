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
    public function up()
    {
    Schema::create('document_bodies', function (Blueprint $table) {
        $table->id();
        $table->foreignIdFor(DocumentHeader::class,'document_id')->onDelete('cascade');
        $table->text('body');
        $table->string('checksum');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_bodies');
    }
};