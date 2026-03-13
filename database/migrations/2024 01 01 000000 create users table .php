<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_users_table
 *
 * Tabla de usuarios compatible con OAuth 2.0.
 * Los campos provider_* almacenan la identidad federada del usuario.
 *
 * Un usuario puede existir con:
 *  a) email + password  (registro tradicional)
 *  b) provider + provider_id  (login via OAuth)
 *  c) Ambos (cuenta vinculada)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();   // null si solo usa OAuth
            $table->string('avatar')->nullable();     // URL avatar del proveedor

            // ── Campos OAuth 2.0 ──────────────────────────────────────────
            // provider: nombre del proveedor (discord, spotify, etc.)
            $table->string('provider')->nullable();
            // provider_id: ID único del usuario en ese proveedor
            $table->string('provider_id')->nullable();
            // access_token: token para llamar a la API del proveedor
            $table->text('access_token')->nullable();
            // refresh_token: para obtener nuevo access_token sin re-login
            $table->text('refresh_token')->nullable();
            // token_expires: timestamp de expiración del access_token
            $table->timestamp('token_expires')->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Un usuario es único por proveedor + ID del proveedor
            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};