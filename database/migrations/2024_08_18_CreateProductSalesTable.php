<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class CreateProductSalesTable extends AbstractMigration
{

    /**
     * Run the migration.
     */
    public function run()
    {
        main()->database()->capsule()->schema()->create('product_sales', function ($table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->default(null);
            $table->foreign('product_id')->references('ID')->on('posts')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->default(null);
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->double("price")->nullable()->default(0);
            $table->dateTime('sold_at')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Optional: Roll back the migration.
     */
    public function rollback()
    {
        main()->database()->capsule()->schema()->dropIfExists('product_sales');
    }

}
