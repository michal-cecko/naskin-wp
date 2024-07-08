<?php

use DeliciousBrains\WPMigrations\Database\AbstractMigration;
use Theme\Models\Other\NameDay;

class PopulateNameDayTable extends AbstractMigration {

	/**
	 * Run the migration.
	 */
	public function run() {
        $nameDays = json_decode(file_get_contents(THEME_PATH . '/database/datasource/namedays.json'), true);
        $existingNameDays = NameDay::all();

        foreach ($nameDays as $day => $name) {

            if ($existingNameDays->where('date', $day)->where("name", $name)->first()) {
                continue;
            }

            NameDay::create([
                'name' => $name,
                'date' => $day,
            ]);
        }
	}

	/**
	 * Optional: Roll back the migration.
	 */
	public function rollback() {}

}
