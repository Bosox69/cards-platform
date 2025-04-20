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
        // Ajouter les clés étrangères pour users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });

        // Ajouter les clés étrangères pour orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('order_status_id')->references('id')->on('order_status');
        });

        // Ajouter les clés étrangères pour order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates');
            $table->foreign('department_id')->references('id')->on('departments');
        });

        // Ajouter les clés étrangères pour templates
        Schema::table('templates', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        // Ajouter d'autres clés étrangères pour les tables restantes...
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les clés étrangères dans l'ordre inverse
        Schema::table('templates', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id', 'template_id', 'department_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'client_id', 'order_status_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });
    }
};
