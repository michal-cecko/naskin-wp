<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class CreateAppointmentsTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        main()->database()->capsule()->schema()->create('appointments', function($table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->default(null);
            $table->foreign('employee_id')->references('id')->on('users')->nullOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('break')->nullable()->default(null);
            $table->foreignId('customer_id')->nullable()->default(null);
            $table->foreign('customer_id')->references('id')->on('posts')->nullOnDelete();
            $table->text('note')->nullable()->default(null);
            $table->string('type');
            $table->string('cancel_token')->nullable()->default(null);
            $table->boolean('has_been_reminded')->default(false);
            $table->string('status');
            $table->timestamps();
        });
	}

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {
        main()->database()->capsule()->schema()->dropIfExists('appointments');
	}

}
