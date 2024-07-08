<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;

class CreateAppointmentPaymentsTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        main()->database()->capsule()->schema()->create('appointment_payments', function($table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->default(null);
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->double('amount');
            $table->text('note')->nullable()->default(null);
            $table->string('type');
            $table->timestamps();
        });
	}

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {
        main()->database()->capsule()->schema()->dropIfExists('appointment_payments');
	}

}
