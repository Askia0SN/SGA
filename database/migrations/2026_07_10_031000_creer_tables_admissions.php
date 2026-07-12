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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 200);
            $table->enum('niveau', ['classe_preparatoire', 'licence', 'master']);
            $table->unsignedInteger('capacite_accueil')->nullable();
            $table->date('date_ouverture');
            $table->date('date_fermeture');
            $table->decimal('frais_scolarite', 10, 2)->nullable();
            $table->text('echeancier_paiement')->nullable();
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['niveau', 'actif']);
            $table->index(['date_ouverture', 'date_fermeture']);
        });

        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->date('date_naissance')->nullable();
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('pays', 100)->nullable();
            $table->text('adresse')->nullable();
            $table->timestamps();
        });

        Schema::create('types_documents', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('nom', 150);
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('programme_type_document', function (Blueprint $table) {
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->foreignId('type_document_id')->constrained('types_documents')->cascadeOnDelete();
            $table->boolean('obligatoire')->default(true);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();

            $table->primary(['programme_id', 'type_document_id']);
        });

        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained('programmes')->restrictOnDelete();
            $table->string('code_suivi', 20)->unique();
            $table->enum('statut', [
                'brouillon',
                'soumise',
                'complement_demande',
                'en_traitement_admission',
                'transmise_au_jury',
                'admise',
                'refusee',
                'abandonnee',
            ])->default('soumise');
            $table->string('derniere_formation')->nullable();
            $table->string('etablissement_origine')->nullable();
            $table->text('lettre_motivation')->nullable();
            $table->text('commentaire_interne')->nullable();
            $table->foreignId('transmise_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decision_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('soumise_le')->nullable();
            $table->timestamp('transmise_au_jury_le')->nullable();
            $table->timestamp('decision_le')->nullable();
            $table->timestamps();

            $table->unique(['candidat_id', 'programme_id']);
            $table->index(['statut', 'soumise_le']);
            $table->index(['programme_id', 'statut']);
        });

        Schema::create('documents_candidature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->foreignId('type_document_id')->nullable()->constrained('types_documents')->nullOnDelete();
            $table->string('nom_original');
            $table->string('chemin_fichier');
            $table->string('type_mime', 100);
            $table->unsignedBigInteger('taille_octets');
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->foreignId('verifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verifie_le')->nullable();
            $table->timestamps();

            $table->index(['candidature_id', 'statut']);
        });

        Schema::create('historiques_candidature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut');
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('acteur', ['candidat', 'service_admission', 'jury', 'super_admin', 'systeme'])->default('systeme');
            $table->text('commentaire')->nullable();
            $table->timestamp('cree_le')->useCurrent();

            $table->index(['candidature_id', 'cree_le']);
        });

        Schema::create('messages_candidature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['note_interne', 'demande_complement', 'message_candidat', 'decision_jury'])->default('note_interne');
            $table->enum('visibilite', ['interne', 'candidat'])->default('interne');
            $table->text('contenu');
            $table->timestamps();

            $table->index(['candidature_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages_candidature');
        Schema::dropIfExists('historiques_candidature');
        Schema::dropIfExists('documents_candidature');
        Schema::dropIfExists('candidatures');
        Schema::dropIfExists('programme_type_document');
        Schema::dropIfExists('types_documents');
        Schema::dropIfExists('candidats');
        Schema::dropIfExists('programmes');
    }
};
