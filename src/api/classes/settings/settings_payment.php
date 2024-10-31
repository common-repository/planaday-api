<?php

class settings_payment extends settings {
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
	 * @param $input
	 *
	 * @return array
	 */
	public function planaday_api_payment_validate( $input ) {
		$message                 = null;
		$type                    = null;
		$valid                   = [];
		$valid['idealtestmodus'] = sanitize_text_field( $input['idealtestmodus'] );
		$valid['cancelbookingafternopayment'] = sanitize_text_field( $input['cancelbookingafternopayment'] );
		$valid['toelichting']    = sanitize_text_field( $input['toelichting'] );
		$valid['bedankttekst']   = $input['bedankttekst'];

		if ( empty( $type ) ) {
			$type    = 'updated';
			$message = __( 'Successfully saved', $this->getClassName() );
		}

		add_settings_error(
			'planaday-api-payment',
			esc_attr( 'settings_updated' ),
			$message,
			$type
		);

		return $valid;
	}

	public static function planaday_is_betaling_mogelijk() {
		$options = get_option( 'planaday-api-general' );

		if ( class_exists( 'Paytium' )
		     && $options['betalingenactief'] === '1' ) {
			return true;
		}

		return false;
	}

	public static function planaday_is_betaling_live() {
		$options = get_option( 'planaday-api-general' );
		$payment = get_option( 'planaday-api-payment' );

		if ( class_exists( 'Paytium' )
		     && $options['betalingenactief'] === '1'
		     && $payment['idealtestmodus'] === '1' ) {
			return false;
		}

		return true;
	}

	public function planaday_is_paymentpage_present() {
		return post_exists( 'Planaday betalings pagina' );
	}

	public function planaday_create_or_update_paymentpage() {
		$contents = '[paytium name="Inschrijven cursus #" description="Inschrijven cursus #" amount="" button_label="Nu betalen"]
[paytium_links auto_redirect /]
[paytium_field type="text" label="course_id" required="true" /]
[paytium_field type="text" label="course_code" required="true" /]
[paytium_field type="text" label="course_name" required="true" /]
[paytium_field type="text" label="course_startdate" required="false" /]
[paytium_field type="text" label="company_address" required="false" /]
[paytium_field type="text" label="company_name" required="false" /]
[paytium_field type="text" label="company_house_number" required="false" /]
[paytium_field type="text" label="company_postal_code" required="false" /]
[paytium_field type="text" label="company_city" required="false" /]
[paytium_field type="text" label="company_phonenumber" required="false" /]
[paytium_field type="text" label="first_name" required="true" /]
[paytium_field type="text" label="last_name" required="true" /]
[paytium_field type="date" label="date_of_birth" required="false" /]
[paytium_field type="email" label="email" required="true" /]
[paytium_field type="text" label="booking_id" required="true" /]
[paytium_field type="text" label="company_email" required="false" /]
[paytium_amount label="Bedrag:" /]
[/paytium]';

        if (self::planaday_is_paymentpage_present()) {
	        wp_update_post(
		        [
			        'ID'             => post_exists( 'Planaday betalings pagina' ),
			        'post_type'      => 'page',
			        'post_content'   => $contents,
			        'post_name'      => 'planadaybetaling',
			        'post_title'     => 'Planaday betalings pagina',
			        'comment_status' => 'closed',
			        'post_status'    => 'publish',
			        'ping_status'    => 'closed'
		        ]
	        );
        } else {
			wp_insert_post(
				[
					'post_type'      => 'page',
					'post_content'   => $contents,
					'post_name'      => 'planadaybetaling',
					'post_title'     => 'Planaday betalings pagina',
					'comment_status' => 'closed',
					'post_status'    => 'publish',
					'ping_status'    => 'closed'
				]
			);
		}
	}


	public function planaday_api_payment_page() {
		$options = get_option( 'planaday-api-general' );
		$payment = get_option( 'planaday-api-payment' );

		echo '<style>.admininfoblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #ff9800; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
		echo '<style>.adminniceblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #00ff00; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
		echo '<style>.adminwarningblok {display: block; line-height: 1.4; padding: 11px 15px; font-size: 14px; text-align: left; margin: 25px 20px 0 2px; background-color: #fff; border-left: 4px solid #ff0000; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);}</style>';
		echo '<div class="wrap">';
		echo '<h2>' . esc_html( get_admin_page_title() ) . ' betalingen</h2>';
		echo '<form method="post" name="planaday_options" action="options.php">';

		if ( self::planaday_is_betaling_mogelijk() && self::planaday_is_betaling_live() ) {
			echo '<div class="adminniceblok">Je omgeving voor betalingen is live! Iedere betaling is een echte betaling</div>';
		} else {
			echo '<div class="admininfoblok">Je zit nu nog in de testmodus. Iedere betaling is een testbetaling.</div>';
		}

		settings_errors();
		settings_fields( 'planaday-api-payment' );
		do_settings_sections( 'planaday-api' );

		if ( ! self::planaday_is_paymentpage_present() ) {
			echo '<div class="admininfoblok">De betalingspagina was niet aanwezig, maar zojuist aangemaakt!</div>';
			self::planaday_create_or_update_paymentpage();
		}

		echo '<div class="adminniceblok">';
		echo '<h1>Betaling checks</h1>';
		echo '<p><b>Wij doen enkele checks om je te helpen: </b></p>';
		echo settings::ChecksAdminPaymentsPage();
		echo '</div>';

		echo '<div class="adminniceblok">Uitgebreide uitleg over betalingen vind je <a href="https://planaday.freshdesk.com/support/solutions/articles/11000080961-online-betalingen-in-wordpress-plugin-instellen-" target="_blank">hier</a></div>';


		if ( class_exists( 'Paytium' ) && $options['betalingenactief'] == 1 && ! empty( $options['betalingenactief'] ) ) {
			$idealmogelijk = true;
		}
		?>

		<?php if ( $idealmogelijk == true ) { ?>
            <h2>Instelllingen voor betalingen</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        Test modus actief?
                    </th>
                    <td>
                        <input type="radio" name="planaday-api-payment[idealtestmodus]"
                               value="1" <?php
						if ( ! isset( $payment['idealtestmodus'] ) || $payment['idealtestmodus'] == '' || $payment['idealtestmodus'] == null || $payment['idealtestmodus'] == '1' ) {
							$payment['idealtestmodus'] = '1';
						}
						if ( $payment['idealtestmodus'] == '1' || $payment['idealtestmodus'] == '' || $payment['idealtestmodus'] == null ) {
							$idealtestmodus = '1';
						} else {
							$idealtestmodus = '0';
						}
						if ( $idealtestmodus == '1' ) {
							echo 'checked="checked"';
						} ?> />
                        Ja
                        <input type="radio" name="planaday-api-payment[idealtestmodus]"
                               value="0" <?php if ( $idealtestmodus == '0' ) {
							echo 'checked="checked"';
						} ?> />
                        Nee
                        <p class="description" id="planaday-api-idealtestmodus">
                            Kies voor 'ja' om testbetalingen te kunnen doen (om te testen)
                            <br><b>Let op: Deze kan enkel op 'nee' worden gezet als er tenminte één testbetaling is gedaan!</b>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row" class="titelrij">
                        Boeking annuleren?
                    </th>
                    <td>
                        <input type="radio" name="planaday-api-payment[cancelbookingafternopayment]"
                               value="1" <?php
                        if (!isset($payment['cancelbookingafternopayment']) || $payment['cancelbookingafternopayment'] === '') {
                            $cancelbookingafternopayment = "0";
                        } else {
                            $cancelbookingafternopayment = $payment['cancelbookingafternopayment'];
                        }
                        if ($cancelbookingafternopayment === '1') {
                            echo 'checked="checked"';
                        } ?> />
                        Ja
                        <input type="radio" name="planaday-api-payment[cancelbookingafternopayment]"
                               value="0" <?php if ($cancelbookingafternopayment === '0' || $cancelbookingafternopayment === '') {
                            echo 'checked="checked"';
                        } ?> />
                        Nee
                        <p class="description" id="planaday-api-payment-cancelbookingafternopayment">
                            Geef aan dat als betaling niet gelukt is of deze dan geannuleerd moet worden in Planaday
                            <br><b>Let op: </b> van een eventuele geannuleerde boeking wordt geen communicatie verstuurd!
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        Toelichting op boekingsformulier
                    </th>
                    <td>
                        <input type='text' class='regular-text' name='planaday-api-payment[toelichting]'
                               value='<?php
						       if ( ! isset( $payment['toelichting'] ) || $payment['toelichting'] == '' || $payment['toelichting'] == null || $payment['toelichting'] == '0' ) {
							       $payment['toelichting'] = 'Na bevestiging kunt u meteen betalen';
						       }
						       if ( $payment['toelichting'] == "" ) {
							       echo "Na bevestiging kunt u meteen betalen";
						       } else {
							       echo $payment['toelichting'];
						       }
						       ?>'/>
                        <p class="description" id="planaday-api-payment[toelichting]">
                            Tekst bij boekingformulier, zodat de klant weet dat zij kunnen betalen<br/>
                            Bijvoorbeeld 'Na bevestiging kunt u meteen betalen'.
                        </p>
                    </td>
                </tr>
            </table>

			<?php
			echo submit_button( __( 'Alle Instellingen opslaan', 'planaday-api-payment' ), 'primary', 'submit', true );
		} else { ?>

            <h2>Instructies voor installatie Paytium</h2>
            Als je gebruik wil maken van iDeal betalingen bij het inschrijven van een cursus, installeer dan
            de plugin
            '<a href="https://www.paytium.nl/" target="_blank">Paytium</a>'. Ga hiervoor naar 'plugins' en
            kies voor
            '<a href="/wp-admin/plugin-install.php">nieuwe plugin</a>' en zoek op 'Paytium' en activeer
            deze.<br/>
            Volg hierna de wizard en zorg ervoor dat je een Mollie account hebt.
		<?php }

		echo '</form>';
		echo '</div>';
	}
}
