<?php

function register_script() {
	wp_enqueue_script( 'fa_jquery', plugins_url( '/planaday-api/assets/js/fontawesome.min.js' ), array(), '6.0', true );
	wp_register_script( 'planaday_calendarmoment', plugins_url( '/planaday-api/assets/js/moment.min.js' ), array( 'jquery' ), '6.0' );
	wp_enqueue_script( 'planaday_calendarmoment' );
	wp_register_script( 'planaday_calendar', plugins_url( '/planaday-api/assets/js/fullcalendar.min.js' ), array( 'jquery' ), '6.0' );
	wp_enqueue_script( 'planaday_calendar' );
	wp_register_script( 'parsley', plugins_url( '/planaday-api/assets/js/parsley.min.js' ), array( 'jquery' ), '6.0' );
	wp_enqueue_script( 'parsley' );
}

function enqueue_style() {
	wp_enqueue_style( 'planaday_fa', plugins_url( '/planaday-api/assets/css/planaday-fa.css' ), false, '6.0', 'all' );
	wp_enqueue_style( 'planaday_calendar_style', plugins_url( '/planaday-api/assets/css/fullcalendar.min.css' ), false, '6.0', 'all' );
	wp_enqueue_style( 'planaday_style', plugins_url( '/planaday-api/assets/css/planaday-style.css' ), false, '8.6', 'all' );
	wp_enqueue_style( 'parsley', plugins_url( '/planaday-api/assets/css/parsley.css' ), false, '6.0', 'all' );
}

function my_register_custom_widget() {
	register_widget( 'widget_cursusdetails' );
	register_widget( 'widget_search' );
}

function editor_scripts() {
	wp_enqueue_style( 'sccss-editor-css', plugins_url( '/planaday-api/assets/css/editor.css' ) );
	if ( codemirror_beschikbaar() ) {
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		wp_enqueue_script( 'sccss-editor-js', plugins_url( '/planaday-api/assets/js/editor.js' ), array( 'jquery' ), '4.0.0', true );
	} else {
		// Maintaining for backwards compatibility.
		wp_enqueue_script( 'sccss-css-lint-js', plugins_url( '/planaday-api/assets/codemirror/csslint.js' ), array( 'sccss-codemirror-js' ),
			'4.0.0', true );
		wp_enqueue_script( 'sccss-codemirror-lint-js', plugins_url( '/planaday-api/assets/codemirror/codemirror-lint.js' ),
			array( 'sccss-css-lint-js' ), '4.0.0', true );
		wp_enqueue_script( 'sccss-codemirror-css-lint-js', plugins_url( '/planaday-api/assets/codemirror/codemirror-css-lint.js' ),
			array( 'sccss-codemirror-css-js' ), '4.0.0', true );
		wp_enqueue_script( 'sccss-codemirror-js', plugins_url( '/planaday-api/assets/codemirror/codemirror.js' ), array(), '4.0.0', true );
		wp_enqueue_script( 'sccss-codemirror-css-js', plugins_url( '/planaday-api/assets/codemirror/css.js' ),
			array( 'sccss-codemirror-lint-js' ), '4.0.0', true );
		wp_enqueue_style( 'sccss-codemirror-css', plugins_url( '/planaday-api/assets/codemirror/codemirror.min.css' ) );
		wp_add_inline_script(
			'sccss-codemirror-js',
			'jQuery( document ).ready( function() {
				var editor = CodeMirror.fromTextArea( document.getElementById( "planaday-api[sccss-content]" ), {
					lineNumbers: true,
					lineWrapping: true,
					mode:"text/css",
					indentUnit: 2,
					tabSize: 2,
					lint: true,
					gutters: [ "CodeMirror-lint-markers" ]
				} );
			} )( CodeMirror );'
		);
	}
}

function codemirror_beschikbaar() {
	$wp_version = get_bloginfo( 'version' );

	return ( version_compare( $wp_version, 4.9 ) >= 0 );
}

function registreer_extra_stylesheet() {
	$url = home_url();

	if ( is_ssl() ) {
		$url = home_url( '/', 'https' );
	}

	wp_register_style(
		'pad_style',
		add_query_arg(
			array(
				'planadaycss' => 1,
			),
			$url
		)
	);

	wp_enqueue_style( 'pad_style' );
}

function sccss_maybe_print_css2() {
	// Only print CSS if this is a stylesheet request.
	if ( ! isset( $_GET['planadaycss'] ) || (int) $_GET['planadaycss'] !== 1 ) {
		return;
	}

	ob_start();
	header( 'Content-type: text/css' );

	sccss_the_css2();

	die();
}

function sccss_the_css2() {
	$options     = get_option( 'planaday-api-css' );
	$raw_content = $options['sccss-content'] ?? '';
	$content     = wp_kses( $raw_content, array( '\'', '\"' ) );
	$content     = str_replace( '&gt;', '>', $content );
	echo $content; // WPCS: xss okay.
}

function paytium_redirect_after_payment( $payment ) {
	return payment::planaday_api_get_instance()->verwerkbetaling( $payment );
}

function pad_cron_update_all_courses() {
	reload_courses_dashboard();
}

function reload_courses_dashboard() {
	$attributes = [
		'start'      => 'now',
		'end'        => '+12months',
		'templateid' => null,
		'label'      => null,
		'fields'     => null,
		'url'        => null
	];

	pad_database::pad_remove_old_data( true );
	pad_database::pad_load_all_data_from_api( $attributes, true );
}

function idxVal( &$var, $default = null ) {
	if ( empty( $var ) ) {
		return $var = $default;
	}

	return $var;
}

function plugin_load_textdomain() {
	load_plugin_textdomain( 'planaday-api', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function pad_is_db_valid(): bool {
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'padcourse';
	$table_name2 = $wpdb->prefix . 'paddayparts';
	$table_name3 = $wpdb->prefix . 'padlocations';

	if ( $wpdb->get_col( "SHOW TABLES LIKE '$table_name1'" )
		&& $wpdb->get_col( "SHOW TABLES LIKE '$table_name2'")
		&& $wpdb->get_col( "SHOW TABLES LIKE '$table_name3'")
	) {
		return true;
	}

	return false;
}

function pad_update_to_latest_version() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'padcourse';
	$table_name2 = $wpdb->prefix . 'paddayparts';
	$table_name3 = $wpdb->prefix . 'padlocations';
	$sql         = [];

	$checkVersion = (float)PLANADAYAPI_CURRENT_VERSION * 100;

	// Inital setup?
	if ( ! pad_is_db_valid() ) {
		pad_first_initial_create_db();
		$checkVersion = 0; // All migrations must be run!
	}

	ob_start();

	// Only update when new version is available
	$pluginVersionDatabase = get_option( 'planaday-api-version' );
	$currentPluginVersion  = (float) $pluginVersionDatabase * 100;
	$pluginVersion         = (float) $checkVersion * 100;
	if ( $currentPluginVersion < $checkVersion || $checkVersion === 0 ) {
		$migrationFiles = glob( __DIR__ . '/migrations/*.php' );
		natsort($migrationFiles );
		// Run migration
		foreach ($migrationFiles  as $filename ) {
			$functionName = str_replace( '.php', '', $filename );
			$functionName = str_replace( __DIR__ . '/migrations/', '', $functionName );

			$migrationVersion = (float) str_replace( '_', '.', $functionName ) * 100;
			$functionName     = 'update' . $functionName;

			if ( $migrationVersion >= $checkVersion ) {
				// Load and run!
				require_once $filename;
				$functionName();
			}
		}
	}
	ob_clean();
}

function pad_update_option( $optionname, $value ) {
	$options                = get_option( 'planaday-api-general' );
	$options[ $optionname ] = $value;
	update_option( 'planaday-api-general', $options );
}

function pad_reload_tables() {
	pad_update_to_latest_version();
}

function pad_first_initial_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name1     = $wpdb->prefix . 'padcourse';
	$table_name2     = $wpdb->prefix . 'paddayparts';
	$table_name3     = $wpdb->prefix . 'padlocations';

	$sql0 = "DROP TABLE IF EXISTS " . $table_name1 . ";";
	$sql1 = "CREATE TABLE IF NOT EXISTS " . $table_name1 . " (
      `id` int(11) NOT NULL PRIMARY KEY,
      `templateid` int(11) DEFAULT NULL,
      `code` varchar(48) DEFAULT NULL,
      `name` varchar(200) DEFAULT NULL,
      `description` text DEFAULT NULL,
      `type` varchar(15) DEFAULT NULL,
      `status` varchar(24) DEFAULT NULL,
      `daypart_amount` int(11) DEFAULT NULL,
      `usersmin` int(11) DEFAULT NULL,
      `usersmax` int(11) DEFAULT NULL,
      `options` int(11) DEFAULT NULL,
      `usersavailable` int(11) DEFAULT NULL,
      `costsusers` float(8,2) DEFAULT NULL,
      `costsvat` float(8,2) DEFAULT NULL,
      `costsremark` text DEFAULT NULL,
      `start_guaranteed` int(11) DEFAULT NULL,
      `moneyback_guaranteed` int(11) DEFAULT NULL,
      `has_elearning` int(11) DEFAULT NULL,
      `has_code95` int(11) DEFAULT NULL,
      `has_soob` int(11) DEFAULT NULL,
      `labels` text DEFAULT NULL,
      `level` varchar(200) DEFAULT NULL,
      `image` blob DEFAULT NULL,
      `language` varchar(50) DEFAULT NULL,
      `lastupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

	$sql2 = 'DROP TABLE IF EXISTS ' . $table_name2 . ';';
	$sql3 = "CREATE TABLE IF NOT EXISTS " . $table_name2 . " (
      `id` int(11) NOT NULL PRIMARY KEY,
      `courseid` int(11) DEFAULT NULL,
      `name` varchar(200) DEFAULT NULL,
      `start_time` time DEFAULT NULL,
      `end_time` time DEFAULT NULL,
      `date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `locationid` int(11) DEFAULT NULL,
      `is_elearning` int(11) DEFAULT NULL,
      `has_code95` int(11) DEFAULT NULL,
      `labels` text DEFAULT NULL,
      `description` text DEFAULT NULL,
      `lastupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

	$sql4 = 'DROP TABLE IF EXISTS ' . $table_name3 . ';';
	$sql5 = "CREATE TABLE IF NOT EXISTS " . $table_name3 . " (
      `id` int(11) NOT NULL PRIMARY KEY,
      `name` varchar(250) DEFAULT NULL,
      `street_1` varchar(60) DEFAULT NULL,
      `street_2` varchar(60) DEFAULT NULL,
      `housenumber` varchar(12) DEFAULT NULL,
      `housenumber_extension` varchar(6) DEFAULT NULL,
      `zipcode` varchar(8) DEFAULT NULL,
      `city` varchar(56) DEFAULT NULL,
      `country` varchar(46) DEFAULT NULL,
      `lat` varchar(24) DEFAULT NULL,
      `lng` varchar(24) DEFAULT NULL,
      `phonenumber_1` varchar(14) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `website` varchar(100) DEFAULT NULL,
      `description` text DEFAULT NULL,
      `capacity` varchar(12) DEFAULT NULL,
      `lastupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$wpdb->query( $sql0 );
	$wpdb->query( $sql1 );
	$wpdb->query( $sql2 );
	$wpdb->query( $sql3 );
	$wpdb->query( $sql4 );
	$wpdb->query( $sql5 );
}
