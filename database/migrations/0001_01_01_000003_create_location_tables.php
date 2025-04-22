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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('mot_de_passe');
            $table->enum('role', ['client', 'proprietaire']);
            $table->string('image_profile')->nullable();
            $table->string('cin_recto')->nullable();
            $table->string('cin_verso')->nullable();
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('mot_pass');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('objets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->string('ville');
            $table->decimal('prix_journalier', 10, 2);
            $table->string('etat');
            $table->foreignId('categorie_id')->constrained('categories');
            $table->foreignId('proprietaire_id')->constrained('utilisateurs');
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->foreignId('objet_id')->constrained('objets')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->date('date_publication');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('statut');
            $table->boolean('premium')->default(false);
            $table->string('adresse');
            $table->foreignId('objet_id')->constrained('objets')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('utilisateurs');
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('utilisateurs');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('statut');
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('note');
            $table->text('commentaire');
            $table->date('date');
            $table->foreignId('objet_id')->constrained('objets');
            $table->foreignId('evaluateur_id')->constrained('utilisateurs');
            $table->foreignId('evalue_id')->constrained('utilisateurs');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('contenu');
            $table->text('contenu_email')->nullable();
            $table->boolean('envoyee')->default(false);
            $table->boolean('lue')->default(false);
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->foreignId('annonce_id')->nullable()->constrained('annonces');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations');
            $table->timestamps();
        });

        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->text('contenu');
            $table->string('statut');
            $table->date('date_creation');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->foreignId('reservation_id')->constrained('reservations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamations');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('annonces');
        Schema::dropIfExists('images');
        Schema::dropIfExists('objets');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('utilisateurs');
    }
};