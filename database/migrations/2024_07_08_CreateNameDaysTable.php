<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class CreateNameDaysTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        main()->database()->capsule()->schema()->create('name_days', function($table) {
            $table->id();
            $table->string('name');
            $table->string("date")->unique();
            $table->timestamps();
        });
	}

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {
        main()->database()->capsule()->schema()->dropIfExists('name_days');
    }

}
