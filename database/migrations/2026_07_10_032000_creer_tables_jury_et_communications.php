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
        Schema::create('avis_jury', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->foreignId('jury_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['admettre', 'refuser', 'demander_complement']);
            $table->text('commentaire')->nullable();
            $table->timestamp('decide_le');
            $table->timestamps();

            $table->unique(['candidature_id', 'jury_id']);
            $table->index(['decision', 'decide_le']);
        });

        Schema::create('modeles_emails', function (Blueprint $table) {
            $table->id();
            $table->string('evenement', 80)->unique();
            $table->string('objet');
            $table->longText('contenu_html');
            $table->text('signature')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('emails_envoyes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->nullable()->constrained('candidatures')->nullOnDelete();
            $table->foreignId('candidat_id')->nullable()->constrained('candidats')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('evenement', 80)->nullable();
            $table->string('destinataire_email');
            $table->string('objet');
            $table->longText('contenu_html')->nullable();
            $table->enum('statut', ['en_attente', 'envoye', 'echec'])->default('en_attente');
            $table->json('donnees')->nullable();
            $table->text('message_erreur')->nullable();
            $table->timestamp('envoye_le')->nullable();
            $table->timestamps();

            $table->index(['statut', 'created_at']);
            $table->index(['candidature_id', 'evenement']);
        });

        Schema::create('notifications_internes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 80);
            $table->string('message');
            $table->json('donnees')->nullable();
            $table->timestamp('lu_le')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'lu_le']);
        });

        Schema::create('journaux_actions', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('acteur');
            $table->string('action', 120);
            $table->nullableMorphs('cible');
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('cree_le')->useCurrent();

            $table->index(['action', 'cree_le']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journaux_actions');
        Schema::dropIfExists('notifications_internes');
        Schema::dropIfExists('emails_envoyes');
        Schema::dropIfExists('modeles_emails');
        Schema::dropIfExists('avis_jury');
    }
};
