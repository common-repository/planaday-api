<?php

class settings_support extends settings {

	public $_className;
	public $_options;

	public function __construct() {
		$this->_className = 'planaday-api';
		$this->_options   = get_option( 'planaday-api-general' );

		foreach ( $this->_options as $key => $value ) {
			if ( $value === null || $value === '' ) {
				$this->_options[ $key ] = '0';
			}
		}
	}

	/**
	 * @return settingsWelcome
	 */
	public static function planaday_api_get_instance() {
		static $instance;

		if ( $instance === null ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Show list of shortcodes
	 */
	public function planaday_api_support() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = get_option( 'planaday-api-general' );

		echo '<div class="wrap">';
		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';


		$active_tab = $_GET['tab'] ?? 'manual';
		echo '<style>.admininfoblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #ff9800; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
		echo '<style>.adminniceblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #00ff00; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
		?>

        <h2 class="nav-tab-wrapper">
            <a href='?page=planaday-api-support&tab=manual'
               class="nav-tab <?php echo $active_tab === 'manual' ? 'nav-tab-active' : ''; ?>">
                Handleiding</a>
            <a href='?page=planaday-api-support&tab=shortcodes'
               class="nav-tab <?php echo $active_tab === 'shortcodes' ? 'nav-tab-active' : ''; ?>">
                Shortcodes</a>
            <a href="?page=planaday-api-support&tab=database"
               class="nav-tab <?php echo $active_tab === 'database' ? 'nav-tab-active' : ''; ?>">
                Database herstel</a>
            <a href="?page=planaday-api-support&tab=settingstring"
               class="nav-tab <?php echo $active_tab === 'settingstring' ? 'nav-tab-active' : ''; ?>">
                Supportstring</a>
        </h2>

        <table class="form-table">
			<?php
			switch ( $active_tab ) {
				case 'database':
					$this->planaday_api_database( $options );
					break;
				case 'settingstring':
					$this->planaday_api_settingstring( $options );
					break;
				case 'manual':
					$this->planaday_api_manual( $options );
					break;
				case 'shortcodes':
					$this->planaday_api_shortcodes( $options );
					break;
			}
			?>
        </table>
        </div>
		<?php
	}


	public function planaday_api_shortcodes() {
		$shortcodes = '<style>table, tr, td {border: 1px solid #dddddd;} table {width: 100%}</style>';
		$shortcodes .= '<h2>Uitleg shortcodes en mogelijkheden:</h2>Gebruik onderstaande shortcodes (onderaan deze pagina) in een bepaalde pagina om een overzicht te krijgen van alle cursussen van dat
            cursussjabloon. Dat kan zijn: <b>courselistblock</b>, <b>courselisttable</b> of <b>courselistlist</b>.<br /><br />';
		$shortcodes .= '<div class="adminniceblok"><h2>Handing om te weten!</h2>Shortcodes kun je uibreiden met: <b>start=now en end=+4months</b>. Je haalt alle cursussen op als je geen templateid of label gebruikt. <br/>Gebruik je bijvoorbeeld <b>templatedid=1</b>
            , dan haal je enkel alle cursussen op van dat cursussoort. <br/>Je kunt ook <b>label=bhv</b> gebruiken om bijvoorbeeld alle cursussen op te halen met dat betreffende label! </div>';
		$shortcodes .= '<div class="adminniceblok"><h3>Andere handige shortcodes: </h3>Deze zijn toepasbaar op iedere pagina. Nummer id=3 verwijst naar cursus 3 in jouw Planaday omgeving: <br />
<ul>
<li>([pad-name id=3]) geeft naam van cursus</li>
<li>([pad-dates id=3]) geeft lijst met datums van dagdelen</li>
<li>([pad-dates-locations id=3]) geeft lijst met datums van dagdelen inclusief locaties</li>
<li>([pad-price id=3]) geeft prijs per persoon van cursus</li>
<li>([pad-price-remark id=3]) geeft opmerkingen bij prijs</li>
<ul>([pad-button id=3]) geeft een button met link naar de juiste cursus</li> 
</ul>';
		$shortcodes .= '</div><br/>';

		$client     = client::planaday_api_get_instance();
		$data       = $client->call(
			$this->_options['url'],
			$this->_options['key'],
			'coursetemplate/list',
			[]
		);
		$shortcodes .= '<table>';
		$shortcodes .= '<tr>';
		$shortcodes .= '<th><strong>Cursussjabloon</strong></th>';
		$shortcodes .= '<th colspan="3">WP shortcode voor in pagina:</th>';
		$shortcodes .= '</tr>';
		foreach ( $data['coursetemplates'] as $course ) {
			$shortcodes .= '<tr>';
			$shortcodes .= '<td><strong>' . $course['name'] . '</strong></td>';
			$shortcodes .= '<td>' . esc_attr( '&#91;' ) . 'courselistblock start=now end=+4months templateid=' . $course['id'] . esc_attr( '&#93;' ) . '</td>';
			$shortcodes .= '<td>' . esc_attr( '&#91;' ) . 'courselisttable start=now end=+4months templateid=' . $course['id'] . esc_attr( '&#93;' ) . '</td>';
			$shortcodes .= '<td>' . esc_attr( '&#91;' ) . 'courselistlist start=now end=+4months templateid=' . $course['id'] . esc_attr( '&#93;' ) . '</td>';
			$shortcodes .= '</tr>';
		}
		$shortcodes .= '</table>';

		echo '<h1>Shortcodes</h1>';
		echo $shortcodes;
	}

	public function planaday_api_database() {
		global $wpdb;

		echo '<h1>Database herstel</h1>';
		$charset_collate = $wpdb->get_charset_collate();

		echo '<div>';
		echo 'Bij eerste keer inlezen van een pagina waar een shortcode staat voor een overzicht van cursussen worden deze meteen opgehaald via de API.</br>';
		echo 'Deze worden de eerste keer en na verloop van tijd welke is ingesteld (zie instellingen) opnieuw opgehaald.</br>';
		echo 'Hierdoor wordt het laden van een pagina veel sneller omdat het lokaal wordt opgeslagen wordt opgehaald. </p>';
		echo '<b>Let op: </b>De detailpagina is altijd actueel, daar wordt de data niet uit de database gehaald, maar via de API.</p>';
		echo '<h2>Aantallen</h2>';
		echo '<p>Aantal cursussen: ' . pad_database::pad_count_rows( 'course', 0 );
		echo '<p>Aantal dagdelen: ' . pad_database::pad_count_rows( 'dayparts', 0 );
		echo '<p>Aantal locaties: ' . pad_database::pad_count_rows( 'locations', 0 );
		echo '<h2>Datum & tijd (van de server)</h2>';
		echo '<p>Laatste update: ' . pad_database::pad_get_lastupdate();
		echo '<p>Huidige tijd: ' . Planaday_date::current_datetime();
		echo '<p>Charset: <b>' . $charset_collate . '</b></p></div>';


		echo '<div class="adminniceblok">';
		echo '<h1>Database acties</h1>';
		if ( isset( $_POST['test_button'] )
		     && check_admin_referer( 'test_button_clicked' ) ) {
			reload_courses_dashboard();
		}
		if ( isset( $_POST['test2_button'] )
		     && check_admin_referer( 'test2_button_clicked' ) ) {
			pad_first_initial_create_db();
		}
		if ( isset( $_POST['upgrade_db'] )
		     && check_admin_referer( 'upgrade_button_clicked' ) ) {
			pad_update_to_latest_version();
		}

		echo '<p><b>Let op:</b> Wacht na klikken totdat deze pagina is herladen. Daarna <a href="/wp-admin/admin.php?page=planaday-api-support&tab=database">ververs je deze pagina</a> om de nieuwe aantallen te zien (en laatste tijd van update)';
		echo '<div><form action="admin.php?page=planaday-api-support&tab=database" method="post">';
		wp_nonce_field( 'test_button_clicked' );
		echo '<input type="hidden" value="true" name="test_button" />';
		submit_button( 'Alle cursussen opnieuw inladen' );
		echo '</form></div>';
		echo '<div><form action="admin.php?page=planaday-api-support&tab=database" method="post">';
		wp_nonce_field( 'test2_button_clicked' );
		echo '<input type="hidden" value="true" name="test2_button" />';
		submit_button( 'Alle tabellen opnieuw opbouwen' );
		echo '</form>';

		echo '<form action="admin.php?page=planaday-api-support&tab=database" method="post">';
		wp_nonce_field( 'upgrade_button_clicked' );
		echo '<input type="hidden" value="true" name="upgrade_db" />';
		submit_button( 'Tabellen bijwerken naar laatste versie' );
		echo '</form></div>';
		echo '<p><b>Tip:</b> Gebruik deze enkel als de tabellen opnieuw geladen moeten worden.';

		echo '</div>';

	}

	public function planaday_api_settingstring() {
		echo '<h1>Supportstring</h1>';

		echo '<div>';
		echo '<p>Om goed te kunnen helpen bij support op de plugin kunnen we soms vragen om onderstaande string naar ons toe te sturen.</p>';
		echo '<p>In deze string staan de instellingen zoals je die gedaan hebt voor je Planaday plugin.</p>';
		$optionsTemp = $this->_options;
		unset( $optionsTemp['bedankttekst'], $optionsTemp['materialtext'], $optionsTemp['mailbedankttekst'], $optionsTemp['bedankttekstmislukt'], $optionsTemp['mailbedankttekstbedrijf'] );
		echo '<textarea rows="20" cols="200">' . serialize( $optionsTemp ) . '</textarea>';
		echo '</div>';
	}

	public function planaday_api_manual() {
		echo '<h1>Handleiding & meer informatie</h1>';

		echo '<h2>In het kort:</h2>';
		echo '<p>Stap 1. Maak één pagina aan voor cursusoverzicht met bijvoorbeeld: <b>[courselistblock start=now end=+4months]</b><br/>';
		echo 'Stap 2. Maak één nieuwe pagina aan en zet hierin de shortcode: <b>[pad-course]</b>. Op deze pagina zal dan de gekozen cursus worden getoond.<br/>';
		echo 'Stap 3. Wil je ook dat de bezoeker kan boeken? Plaats dan onder <b>[pad-course]</b> de volgende shortcode: <b>[pad-bookingform]</b><br/>';
		echo 'Stap 4. Hergenereer de pagina verwijzingen via: Wordpress -> instellingen -> Permalinks -> Opslaan';
		echo '<br><br>Je kunt ook nog widgets toevoegen aan je sidebar. Deze toon details van de cursus indien je de cursus bekijkt. Instellingen hiervan doe je in de widget zelf.</p>';

		echo '<div class="adminniceblok"><h2>Meer informatie?</h2>Meer uitgebreide informatie vind je in onze handleiding. Zie ook <a href="https://planaday.freshdesk.com/support/solutions/articles/11000058859-wordpress-in-website-met-publieke-api" target="_blank">hier</a></div>';

	}
}
