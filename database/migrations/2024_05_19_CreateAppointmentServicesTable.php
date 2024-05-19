<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class CreateAppointmentServicesTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        main()->database()->capsule()->schema()->create('appointment_services', function($table) {
            $table->id();
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreignId('appointment_id');
            $table->foreign('appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
            $table->integer('duration');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
	}

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {
	}

}
