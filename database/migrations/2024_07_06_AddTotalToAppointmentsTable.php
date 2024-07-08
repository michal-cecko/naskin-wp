<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;

class AddTotalToAppointmentsTable extends AbstractMigration {

    /**
     * Run the migration.
     */
    public function run() {
        main()->database()->capsule()->schema()->table('appointments', function($table) {
            $table->double('total')->default(0);
        });
    }

    /**
     * Optional: Roll back the migration.
     */
    public function rollback() {
        main()->database()->capsule()->schema()->table('appointments', function($table) {
            $table->dropColumn(['total']);
        });
    }

}
