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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('folio')->unique();

            $table->enum('request_type', [
                'queja',
                'sugerencia',
                'incidente',
                'mal_trato',
                'acoso',
                'condiciones_laborales',
                'problemas_colaboradores',
                'otro',
            ]);

            $table->boolean('is_anonymous')->default(false);
            $table->string('full_name')->nullable();
            $table->string('department');
            $table->string('location');
            $table->date('incident_date')->nullable();
            $table->longText('description');
            $table->text('involved_people')->nullable();

            $table->enum('urgency_level', ['bajo', 'medio', 'alto', 'critico']);

            $table->boolean('has_evidence')->default(false);
            $table->boolean('wants_follow_up')->default(false);
            $table->string('contact_info')->nullable();
            $table->boolean('accepted_terms')->default(false);

            $table->enum('status', ['recibido', 'en_revision', 'atendido', 'cerrado', 'descartado'])
                ->default('recibido');
            $table->longText('internal_notes')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['urgency_level']);
            $table->index(['request_type']);
            $table->index(['department']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
