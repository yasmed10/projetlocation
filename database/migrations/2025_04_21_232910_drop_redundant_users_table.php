<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Dans le fichier database/migrations/xxxx_xx_xx_xxxxxx_drop_redundant_users_table.php

public function up(): void {
    Schema::dropIfExists('users'); // Ceci supprime la table 'users'
}

public function down(): void {
    // Optionnel mais recommandé: Recréer la table si on rollback
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Ajustez si la structure originale était différente
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}

  
};
