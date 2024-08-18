<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class CreateExpensesTable extends AbstractMigration
{

    /**
     * Run the migration.
     */
    public function run()
    {
        main()->database()->capsule()->schema()->create('expenses', function ($table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->default(null);
            $table->foreign('category_id')->references('term_id')->on('terms')->nullOnDelete();
            $table->text("description")->nullable();
            $table->string("note")->nullable();
            $table->string("supplier")->nullable();
            $table->double("price");
            $table->timestamps();
        });
    }

    /**
     * Optional: Roll back the migration.
     */
    public function rollback()
    {
        main()->database()->capsule()->schema()->dropIfExists('expenses');
    }

}
