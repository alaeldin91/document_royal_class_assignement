<?php

use App\Models\Permission;
use App\Models\Role;
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
         Schema::create('role_permission', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Role::class,'role_id')->onDelete('cascade');
                $table->foreignIdFor(Permission::class,'permission_id')->onDelete('cascade');
                $table->timestamps();
            });
        }
        
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};
