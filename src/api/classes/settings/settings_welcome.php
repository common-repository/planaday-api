<?php


class settings_welcome extends settings
{

	public static function planaday_api_get_instance() {
		static $instance;

		if ( $instance === null ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
     *
     */
    public function planaday_api_welcome_page()
    {
        global $wpdb;

        $options = get_option('planaday-api-general');
        add_action('widgets_init', 'my_register_custom_widget');

        echo '<style>.admininfoblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #ff9800; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
        echo '<style>.adminniceblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #00ff00; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
        echo '<img src="/wp-content/plugins/planaday-api/assets/logobreed.png" align="right">';
        echo '<h2 style="color: #ca4a1f;">Over Planaday plugin & hulp</h2>
        <h3>
        Met deze plugin plaats je eenvoudig cursusaanbod uit jouw Planaday omgeving in jouw Wordpress-website.<br>
        Hiervoor is een eigen omgeving nodig van Planaday, zie ook <a href="https://www.planaday.nl" target="_blank">https://www.planaday.nl</a> en de publieke API moet zijn geactiveerd.<br><br>
        </h3>';

        if (isset($options['url'])
            && empty($options['url'])) {
            echo '<div class="admininfoblok">';
            echo '<h1>Stappenplan nieuw inrichten</h1>';
            echo '<p>Bekijk de shortcodes pagina en lees de handleiding <a href="https://planaday.freshdesk.com/support/solutions/articles/11000058859-wordpress-in-website-met-publieke-api" target="_blank">hier</a>. Hier staan alle stappen vermeld.</p>';
            echo '</div>';
        }

        $charset_collate = $wpdb->get_charset_collate();
        echo '<div class="adminniceblok">';
        echo '<h2>Welke gegevens kent de plugin?</h2>';
	    echo '<p><b>Versie:</b> ' . PLANADAYAPI_CURRENT_VERSION;
	    echo '<p><b>Huidige tijd:</b> ' . date('d-m-Y H:i:s', strtotime(Planaday_date::current_datetime()));
	    if (pad_database::pad_get_lastupdate() === null) {
		    echo '<p><b>Laatste API update:</b> Nog niet geupdate';
	    } else {
		    echo '<p><b>Laatste API update:</b> ' . date( 'd-m-Y H:i:s', strtotime( pad_database::pad_get_lastupdate() ) );
		    echo '<p><b>Aantal cursussen:</b> ' . pad_database::pad_count_rows('course',0);
		    echo '<p><b>Aantal dagdelen:</b> ' . pad_database::pad_count_rows('dayparts',0);
		    echo '<p><b>Aantal locaties:</b> ' . pad_database::pad_count_rows('locations',0);

	    }
        echo '</div>';


        echo '<div class="admininfoblok">';
        echo '<h2>Algemene checks</h2>';
        echo '<p><b>Wij doen enkele checks om je te helpen: </b></p>';
        echo settings::ChecksAdminSettingsPage();
        echo '</div>';

        if (!empty($options['betalingenactief'])
            && $options['betalingenactief'] == 1) {
            echo '<div class="admininfoblok">';
            echo '<h1>Betaling checks (betaling is actief!)</h1>';
            echo 'Meer informatie hiervoor vindt je hier: <a href="https://planaday.freshdesk.com/support/solutions/articles/11000080961-online-betalingen-in-wordpress-plugin-instellen-" target="_blank">hier handleiding</a> en voer deze goed en stap voor stap door!';
            echo '<p>Ga naar "<a href="/wp-admin/admin.php?page=planaday-api-payment">betalingen</a>" om deze betaling instellingen te wijzigen!</p>';
            echo '</div>';
        } else {
            echo '<div class="adminniceblok">iDeal is beschikbaar in Planaday Wordpress Plugin. Geef aan bij "instellingen" als je dit wil gebruiken. <br/>';
            echo 'Lees hiervoor de <a href="https://planaday.freshdesk.com/support/solutions/articles/11000080961-online-betalingen-in-wordpress-plugin-instellen-" target="_blank">hier handleiding</a> en voer deze goed en stap voor stap door!</div>';
        }
    }
}
