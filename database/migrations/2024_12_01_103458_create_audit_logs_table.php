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
        Schema::create('audit_logs', function (Blueprint $table) {
           
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(DocumentHeader::class,'document_id')->onDelete('cascade');
            $table->string('action');
            $table->json('changes_summary');
            $table->timestamp('timestamp');
            $table->timestamps();
            $table->string('ip_address');
            $table->string('user_agent');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
