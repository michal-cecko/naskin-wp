<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class AddPaymentTypeToProductSalesTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        main()->database()->capsule()->schema()->table('product_sales', function($table) {
            $table->enum('payment_type', ['cash', 'bank_card'])->nullable()->default(null);
        });
    }

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {
        main()->database()->capsule()->schema()->table('product_sales', function($table) {
            $table->dropColumn(['payment_type']);
        });
	}
}
