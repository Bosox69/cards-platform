<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si les contraintes existent déjà avant de les ajouter
        Schema::table('order_items', function (Blueprint $table) {
            // Vérifiez si la contrainte n'existe pas déjà
            if (!$this->constraintExists('order_items', 'order_items_order_id_foreign')) {
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            }
            
            if (!$this->constraintExists('order_items', 'order_items_template_id_foreign')) {
                $table->foreign('template_id')->references('id')->on('templates');
            }
            
            if (!$this->constraintExists('order_items', 'order_items_department_id_foreign')) {
                $table->foreign('department_id')->references('id')->on('departments');
            }
        });

        // Ajouter les clés étrangères pour templates
        Schema::table('templates', function (Blueprint $table) {
            if (!$this->constraintExists('templates', 'templates_department_id_foreign')) {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            }
        });

        Schema::table('card_data', function (Blueprint $table) {
            if (!$this->constraintExists('card_data', 'card_data_order_item_id_foreign')) {
                $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            }
        });
    }

    /**
     * Vérifie si une contrainte de clé étrangère existe déjà
     */
    private function constraintExists($table, $constraintName)
    {
        $constraints = DB::select("SHOW CREATE TABLE {$table}");
        return strpos($constraints[0]->{'Create Table'}, $constraintName) !== false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Il est plus sûr de ne rien faire dans down() si nous ne sommes pas certains
        // des contraintes qui ont été ajoutées par cette migration
    }
};
