<?php

function update9_2()
{
    global $wpdb;

    pad_database::pad_remove_old_data(true);

	$table_name1 = $wpdb->prefix . 'padcourse';
	$table_name2 = $wpdb->prefix . 'paddayparts';
	$table_name3 = $wpdb->prefix . 'padlocations';
	$sql         = [];

	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name1 LIKE 'level'" ) ) {
		$sql[] = "ALTER TABLE $table_name1 ADD level varchar(200) DEFAULT NULL";
	}
	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name1 LIKE 'image'" ) ) {
		$sql[] = "ALTER TABLE $table_name1 ADD image blob DEFAULT NULL";
	}
	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name1 LIKE 'language'" ) ) {
		$sql[] = "ALTER TABLE $table_name1 ADD language varchar(50) DEFAULT NULL";
	}
	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name1 LIKE 'options'" ) ) {
		$sql[] = "ALTER TABLE $table_name1 ADD options int(11) DEFAULT NULL";
	}
	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name2 LIKE 'description'" ) ) {
		$sql[] = "ALTER TABLE  $table_name2 ADD description text DEFAULT NULL";
	}
	if ( ! $wpdb->get_col( "SHOW COLUMNS FROM $table_name2 LIKE 'end_date'" ) ) {
		$sql[] = "ALTER TABLE  $table_name2 ADD end_date date DEFAULT NULL";
	}

    foreach ($sql as $sqlline) {
        $wpdb->query($sqlline);
    }

	update_option( 'planaday-api-version', '9.2' );
}
