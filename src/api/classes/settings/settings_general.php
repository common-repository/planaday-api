<?php

class settings_general extends settings {
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
	public function planaday_api_validate( $input ) {
		$message = null;
		$type    = null;

		// Prefill valid with option values
		$valid = get_option( 'planaday-api-general' );

		// Refill with input
		if ( array_key_exists( 'tab', $input ) ) {
			$valid['tab'] = sanitize_text_field( $input['tab'] );
		}

//        foreach($input as $key=>$value) {
//            echo sprintf(
//                    "pad_update_option('%s', '%s');",
//                    $key,
//                    $value
//            ) . '<br>';
//        }
//        die();

		switch ( $valid['tab'] ) {
			case 'general':
				$valid['bedankttekstmislukt']         = $input['bedankttekstmislukt'];
				$valid['bedanktredirectmislukt']      = sanitize_text_field( $input['bedanktredirectmislukt'] );
				$valid['bedankttekst']                = $input['bedankttekst'];
				$valid['bedanktredirect']             = sanitize_text_field( $input['bedanktredirect'] );
				$valid['toondebuginfo']               = sanitize_text_field( $input['toondebuginfo'] );
				$valid['mailcursusaanmelding']        = sanitize_text_field( $input['mailcursusaanmelding'] );
				$valid['dagdelentekst']               = sanitize_text_field( $input['dagdelentekst'] );
				$valid['betalingenactief']            = sanitize_text_field( $input['betalingenactief'] );
				$valid['optiesmeetellen']             = sanitize_text_field( $input['optiesmeetellen'] );
				$valid['skipcoursewithonlyelearning'] = sanitize_text_field( $input['skipcoursewithonlyelearning'] );
				$valid['btwinofexbtwtonen']           = sanitize_text_field( $input['btwinofexbtwtonen'] );
				$valid['btwinofexbtwtonenlabel']      = sanitize_text_field( $input['btwinofexbtwtonenlabel'] );
				$valid['bedankurl']                   = sanitize_text_field( $input['bedankurl'] );
				$valid['bedankurlmislukt']            = sanitize_text_field( $input['bedankurlmislukt'] );
				$valid['toonomschrijvingcursus']      = sanitize_text_field( $input['toonomschrijvingcursus'] );
				break;
			case 'api':
				$valid['url'] = esc_url( $input['url'] );
				$valid['key'] = sanitize_text_field( $input['key'] );
				break;
			case 'courseoverview':
				$valid['tooncode95overzicht']                = sanitize_text_field( $input['tooncode95overzicht'] );
				$valid['toonsooboverzicht']                  = sanitize_text_field( $input['toonsooboverzicht'] );
				$valid['toonelearningdagdeelinoverzicht']    = sanitize_text_field( $input['toonelearningdagdeelinoverzicht'] );
				$valid['toondetaildagdelenlijstinoverzicht'] = sanitize_text_field( $input['toondetaildagdelenlijstinoverzicht'] );
				$valid['toonlocatiedagdeeloverzicht']        = sanitize_text_field( $input['toonlocatiedagdeeloverzicht'] );
				$valid['toonlabelindagdeel']                 = sanitize_text_field( $input['toonlabelindagdeel'] );
				$valid['tooncostsremarkoverzicht']           = sanitize_text_field( $input['tooncostsremarkoverzicht'] );
				$valid['tooncursuselearning']                = sanitize_text_field( $input['tooncursuselearning'] );
				$valid['tooncursuslabelsoverzicht']          = sanitize_text_field( $input['tooncursuslabelsoverzicht'] );
				$valid['toondagdelen']                       = sanitize_text_field( $input['toondagdelen'] );
				$valid['toonlocatiebijoverzicht']            = sanitize_text_field( $input['toonlocatiebijoverzicht'] );
				$valid['toonprijs']                          = sanitize_text_field( $input['toonprijs'] );
				$valid['toonvollecursus']                    = sanitize_text_field( $input['toonvollecursus'] );
				$valid['tooncursusdagdelenverleden']         = sanitize_text_field( $input['tooncursusdagdelenverleden'] );
				$valid['toonbutton']                         = sanitize_text_field( $input['toonbutton'] );
				$valid['startgarantieoverzicht']             = sanitize_text_field( $input['startgarantieoverzicht'] );
				$valid['geldterugoverzicht']                 = sanitize_text_field( $input['geldterugoverzicht'] );
				$valid['limiettekstomschrijving']            = sanitize_text_field( $input['limiettekstomschrijving'] );
				$valid['tekstgeencursussen']                 = $input['tekstgeencursussen'];
				$valid['buttontekstoverzicht']               = sanitize_text_field( $input['buttontekstoverzicht'] );
				$valid['toonimginoverzicht']                 = sanitize_text_field( $input['toonimginoverzicht'] );
				break;
			case 'coursedetail':
				$valid['toonsoobdetailcursus']    = sanitize_text_field( $input['toonsoobdetailcursus'] );
				$valid['tooncode95cursus']        = sanitize_text_field( $input['tooncode95cursus'] );
				$valid['tooncursuslabelsdetail']  = sanitize_text_field( $input['tooncursuslabelsdetail'] );
				$valid['toondetaildagdelen']      = sanitize_text_field( $input['toondetaildagdelen'] );
				$valid['toondetaildagdelenlijst'] = sanitize_text_field( $input['toondetaildagdelenlijst'] );
				$valid['toonlocatiedagdeel']      = sanitize_text_field( $input['toonlocatiedagdeel'] );
				$valid['toonelearningdagdeel']    = sanitize_text_field( $input['toonelearningdagdeel'] );
				$valid['toonbeschikbareplaatsen'] = sanitize_text_field( $input['toonbeschikbareplaatsen'] );
				$valid['startgarantiedetail']     = sanitize_text_field( $input['startgarantiedetail'] );
				$valid['geldterugdetail']         = sanitize_text_field( $input['geldterugdetail'] );
				$valid['tooncostsremark']         = sanitize_text_field( $input['tooncostsremark'] );
				$valid['toonprijsdetailpagina']   = sanitize_text_field( $input['toonprijsdetailpagina'] );
				$valid['countelearningasdaypart'] = sanitize_text_field( $input['countelearningasdaypart'] );
				$valid['toonniveau']              = sanitize_text_field( $input['toonniveau'] );
				$valid['toonomschrijvingdagdeel'] = sanitize_text_field( $input['toonomschrijvingdagdeel'] );
				$valid['toonimgindetail']         = sanitize_text_field( $input['toonimgindetail'] );
				break;
			case 'calender':
				$valid['calendarcolorback']     = sanitize_text_field( $input['calendarcolorback'] );
				$valid['calendarcolortext']     = sanitize_text_field( $input['calendarcolortext'] );
				$valid['tooncalendarmouseover'] = sanitize_text_field( $input['tooncalendarmouseover'] );
				break;
			case 'materials':
				$valid['materialbookingactive'] = sanitize_text_field( $input['materialbookingactive'] );
				$valid['materialtitle']         = sanitize_text_field( $input['materialtitle'] );
				$valid['materialtext']          = sanitize_text_field( $input['materialtext'] );
				break;
			case 'search':
				$valid['toonlocatiesbijsearch']  = sanitize_text_field( $input['toonlocatiesbijsearch'] );
				$valid['toonlabelsbijsearch']    = sanitize_text_field( $input['toonlabelsbijsearch'] );
				$valid['toontextbijsearch']      = sanitize_text_field( $input['toontextbijsearch'] );
				$valid['tooncode95bijsearch']    = sanitize_text_field( $input['tooncode95bijsearch'] );
				$valid['toonsoobbijsearch']      = sanitize_text_field( $input['toonsoobbijsearch'] );
				$valid['toonelearningbijsearch'] = sanitize_text_field( $input['toonelearningbijsearch'] );
				$valid['tooninblockvorm']        = sanitize_text_field( $input['tooninblockvorm'] );
				break;
			case 'bookingform':
				$valid['mailcursusaanmeldingcursist']        = sanitize_text_field( $input['mailcursusaanmeldingcursist'] );
				$valid['mailcursusaanmeldingbedrijf']        = sanitize_text_field( $input['mailcursusaanmeldingbedrijf'] );
				$valid['mailbedankttekst']                   = $input['mailbedankttekst'];
				$valid['mailbedankttekstbedrijf']            = $input['mailbedankttekstbedrijf'];
				$valid['voorkeurbooleancompany']             = sanitize_text_field( $input['voorkeurbooleancompany'] );
				$valid['onlybookingelparticulier']           = sanitize_text_field( $input['onlybookingelparticulier'] );
				$valid['vraagroepnaam']                      = sanitize_text_field( $input['vraagroepnaam'] );
				$valid['vraagmeisjesnaam']                   = sanitize_text_field( $input['vraagmeisjesnaam'] );
				$valid['vraagadrescursist']                  = sanitize_text_field( $input['vraagadrescursist'] );
				$valid['vraaghuisnrext']                     = sanitize_text_field( $input['vraaghuisnrext'] );
				$valid['vraagcontactpersoon']                = sanitize_text_field( $input['vraagcontactpersoon'] );
				$valid['vraagcostcentercode']                = sanitize_text_field( $input['vraagcostcentercode'] );
				$valid['vraagcostcentercodemanatory']        = sanitize_text_field( $input['vraagcostcentercodemanatory'] );
				$valid['vraag_internal_reference']           = sanitize_text_field( $input['vraag_internal_reference'] );
				$valid['tekstkostenplaats']                  = sanitize_text_field( $input['tekstkostenplaats'] );
				$valid['vraagpersoneelsnummer']              = sanitize_text_field( $input['vraagpersoneelsnummer'] );
				$valid['toonformdateofbirth']                = sanitize_text_field( $input['toonformdateofbirth'] );
				$valid['toonformdateofbirthmanatory']        = sanitize_text_field( $input['toonformdateofbirthmanatory'] );
				$valid['toonformcountryofbirth']             = sanitize_text_field( $input['toonformcountryofbirth'] );
				$valid['toonformcountryofbirthmanatory']     = sanitize_text_field( $input['toonformcountryofbirthmanatory'] );
				$valid['toonemailinvoice']                   = sanitize_text_field( $input['toonemailinvoice'] );
				$valid['toonemailinvoicemanatory']           = sanitize_text_field( $input['toonemailinvoicemanatory'] );
				$valid['toonformphonenumbermanatory']        = sanitize_text_field( $input['toonformphonenumbermanatory'] );
				$valid['toonformphonenumbercompany']         = sanitize_text_field( $input['toonformphonenumbercompany'] );
				$valid['toonformphonenumbercompanymanatory'] = sanitize_text_field( $input['toonformphonenumbercompanymanatory'] );
				$valid['toonformpositionstudent']            = sanitize_text_field( $input['toonformpositionstudent'] );
				$valid['toonformpositionstudentmanatory']    = sanitize_text_field( $input['toonformpositionstudentmanatory'] );
				$valid['toonoptiecode95']                    = sanitize_text_field( $input['toonoptiecode95'] );
				$valid['tekstcode95optie']                   = sanitize_text_field( $input['tekstcode95optie'] );
				$valid['toonoptiesoob']                      = sanitize_text_field( $input['toonoptiesoob'] );
				$valid['tekstsooboptie']                     = sanitize_text_field( $input['tekstsooboptie'] );
				$valid['toonstudentremark']                  = sanitize_text_field( $input['toonstudentremark'] );
				$valid['teksttitelbooking']                  = sanitize_text_field( $input['teksttitelbooking'] );
				$valid['toonformalgemenevoorwaarden']        = sanitize_text_field( $input['toonformalgemenevoorwaarden'] );
				$valid['urlalgemenevoorwaarden']             = sanitize_text_field( $input['urlalgemenevoorwaarden'] );
				$valid['toonformphonenumber']                = sanitize_text_field( $input['toonformphonenumber'] );
				$valid['toonapiattributen']                  = sanitize_text_field( $input['toonapiattributen'] );
				$valid['vraagfinancieleinfobijbedrijf']      = sanitize_text_field( $input['vraagfinancieleinfobijbedrijf'] );
				break;
			case 'database':
				$valid['dbcoursehours']   = sanitize_text_field( $input['dbcoursehours'] );
				$valid['dbdaypartshours'] = sanitize_text_field( $input['dbdaypartshours'] );
				break;
		}

		if ( empty( $valid['url'] ) ) {
			$type    = 'error';
			$message = __( 'API URL can not be empty', $this->getClassName() );
		} elseif ( empty( $valid['key'] ) ) {
			$type    = 'error';
			$message = __( 'API key can not be empty', $this->getClassName() );
		}

		if ( empty( $type ) ) {
			$type    = 'updated';
			$message = __( 'Successfully saved', $this->getClassName() );
		}

		add_settings_error(
			'planaday-api-general',
			esc_attr( 'settings_updated' ),
			$message,
			$type
		);

		unset( $valid['tab'] );

		return $valid;
	}

	public function planaday_api_admin_page() {
		$options = get_option( 'planaday-api-general' );

		echo '<div class="wrap">';
		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
		echo '<form method="post" name="planaday_options" action="options.php">';


		settings_errors();
		settings_fields( 'planaday-api-general' );
		do_settings_sections( 'planaday-api' );

		if ( isset( $options['dagdelentekst'] )
		     && $options['dagdelentekst'] === '' ) {
			$options['dagdelentekst'] = 'Dagdelen';
		}

		echo submit_button( __( 'Deze instellingen opslaan', 'planaday-api-general' ), 'primary', 'submit', true );
		echo __( '<b>Let op: </b>Sla je wijzigingen per tabblad op', 'planaday-api-general' ) . '</br></br>';

		$active_tab = $_GET['tab'] ?? 'general';

		echo '<input type="hidden" name="planaday-api-general[tab]" value="' . $active_tab . '">';
		?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=planaday-api-general&tab=general"
               class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                Algemeen</a>
            <a href="?page=planaday-api-general&tab=api"
               class="nav-tab <?php echo $active_tab === 'api' ? 'nav-tab-active' : ''; ?>">
                API</a>
            <a href="?page=planaday-api-general&tab=courseoverview"
               class="nav-tab <?php echo $active_tab === 'courseoverview' ? 'nav-tab-active' : ''; ?>">
                Cursus overzicht</a>
            <a href="?page=planaday-api-general&tab=coursedetail"
               class="nav-tab <?php echo $active_tab === 'coursedetail' ? 'nav-tab-active' : ''; ?>">
                Cursus detail</a>
            <a href="?page=planaday-api-general&tab=calender"
               class="nav-tab <?php echo $active_tab === 'calender' ? 'nav-tab-active' : ''; ?>">
                Cursus kalender</a>
            <a href="?page=planaday-api-general&tab=materials"
               class="nav-tab <?php echo $active_tab === 'materials' ? 'nav-tab-active' : ''; ?>">
                Materialen</a>
            <a href="?page=planaday-api-general&tab=search"
               class="nav-tab <?php echo $active_tab === 'search' ? 'nav-tab-active' : ''; ?>">
                Zoeken</a>
            <a href="?page=planaday-api-general&tab=bookingform"
               class="nav-tab <?php echo $active_tab === 'bookingform' ? 'nav-tab-active' : ''; ?>">
                Boekingformulier</a>
            <a href="?page=planaday-api-general&tab=database"
               class="nav-tab <?php echo $active_tab === 'database' ? 'nav-tab-active' : ''; ?>">
                Database</a>
        </h2>

        <table class="form-table">
			<?php
			switch ( $active_tab ) {
				case 'api':
					$this->planaday_api_api_settings( $options );
					break;
				case 'database':
					$this->planaday_api_database_settings( $options );
					break;
				case 'materials':
					$this->planaday_api_materiaal_settings( $options );
					break;
				case 'courseoverview':
					$this->planaday_api_overzicht_settings( $options );
					break;
				case 'coursedetail':
					$this->planaday_api_detail_settings( $options );
					break;
				case 'search':
					$this->planaday_api_zoeken_settings( $options );
					break;
				case 'calender':
					$this->planaday_api_kalender_settings( $options );
					break;
				case 'bookingform':
					$this->planaday_api_formulier_settings( $options );
					break;
				case 'general':
					$this->planaday_api_general_settings( $options );
					break;
				case 'support':
					$this->planaday_api_support_settings( $options );
					break;
			}
			?>

        </table>

		<?php
		echo submit_button( __( 'Alle Instellingen opslaan', 'planaday-api-general' ), 'primary', 'submit', true );

		echo '</form>';
		echo '</div>';
	}

	public function planaday_api_database_settings( $options ) {
		?>

        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Database instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Uren voor cursus-data
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[dbcoursehours]'
                       value='<?php
				       if ( ! isset( $options['dbcoursehours'] )
				            || $options['dbcoursehours'] === "" ) {
					       echo "12";
				       } else {
					       echo $options['dbcoursehours'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-dbcoursehours">
                    Vul hier aantal uur in dat er ververst moet worden, hoe lager de uren, hoe vaker er data
                    opgehaald zal worden. <br>
                    Ons voorstel is 12 uur.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Uren voor dagdelen & locaties data
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[dbdaypartshours]'
                       value='<?php
				       if ( ! isset( $options['dbdaypartshours'] )
				            || $options['dbdaypartshours'] === "" ) {
					       echo "24";
				       } else {
					       echo $options['dbdaypartshours'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-dbcoursehours">
                    Vul hier aantal uur in dat er ververst moet worden, hoe lager de uren, hoe vaker er data
                    opgehaald zal worden<br>
                    Dit voor de locaties & dagdelen informatie die bij een cursus horen.<br>
                    Ons voorstel is 24 uur.
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_api_settings( $options ) {
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">API instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Adres van portal
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[url]'
                       <?php
				       if ( ! array_key_exists( 'url', $options ) || $options['url'] === '' ) {
					       echo " placeholder='https://jouwomgevingnaam.api.planaday.nl'";
				       } else {
					       echo " value='" . $options['url'] . "'";
				       }
				       ?>/>
                <p class="description" id="planaday-api-url">
                    Deze vind je in jouw Planaday bij beheer->koppelingen->publieke api
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                API key
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[key]'
                       value='<?php
				       if ( ! array_key_exists( 'key', $options ) || $options['key'] === '' ) {
					       echo '';
				       } else {
					       echo $options['key'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-key">
                    Deze genereer je zelf in jouw planaday bij beheer->koppelingen->publieke api
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_general_settings( $options ) {
		if ( ! isset( $options['bedankurl'] ) || $options['bedankurl'] === '' || $options['bedankurl'] === null || $options['bedankurl'] === '0' ) {
			$options['bedankurl'] = '0';
		}
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Algemene instellingen</h3></th>
        </tr>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f; text-decoration: underline;">Boeking gelukt!</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Na boeking redirect?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[bedanktredirect]"
                       value="1" <?php
				if ( ! isset( $options['bedanktredirect'] ) || $options['bedanktredirect'] === '' ) {
					$bedanktredirect = "1";
				} else {
					$bedanktredirect = $options['bedanktredirect'];
				}
				if ( $bedanktredirect === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[bedanktredirect]"
                       value="0" <?php if ( $bedanktredirect !== '1' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-bedanktredirect">
                    Wil je dat na het boeken - indien je een pagina hieronder hebt gekozen - dat er een redirect plaatsvind?<br/><br/>
                    <strong>Let op: </strong> enkel van toepassing als je hieronder een pagina hebt gekozen!<br/>
                    <strong>Belangrijk: </strong> kies je voor redirect, dan kunnen 'waardes' zoals cursusnaam en naam deelnemer niet
                    getoond worden
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Selecteer redirect pagina
            </th>
            <td>
                <select name='planaday-api-general[bedankurl]'>
                    <option value='0'><?php _e( 'Selecteer een pagina (geen)', 'planaday-api' ); ?></option>
					<?php $pages = get_pages(); ?>
					<?php foreach ( $pages as $page ) { ?>
                        <option value='<?php echo $page->ID; ?>' <?php selected( $options['bedankurl'],
							$page->ID ); ?> ><?php echo $page->post_title; ?></option>
					<?php }; ?>
                </select>
                <p class="description" id="planaday-api-general-bedankurl">
                    Kies enkel een pagina als je een aparte 'bedank' pagina hebt gemaakt.<br/><br/>
                    <strong>-- of --</strong><br/><br/>
                    Vul hieronder een "Bedankt tekst na boeking" in:
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Bedankt tekst na boeking
            </th>
            <td>
				<?php
				if ( ! isset( $options['bedankttekst'] ) || $options['bedankttekst'] === '' ) {
					$mailbedankttekst = "Hallo {naam},\n\n";
					$mailbedankttekst .= "Bedankt voor je aanmelding voor de cursus: {cursus} met startdatum: {startdatum}.\n";
					$mailbedankttekst .= "Zodra wij deze hebben verwerkt krijg je nog een definitieve bevestiging per E-mail.\n\n";
				} else {
					$mailbedankttekst = $options['bedankttekst'];
				}
				wp_editor( $mailbedankttekst, 'bedankttekst', array(
					'textarea_name' => 'planaday-api-general[bedankttekst]',
					'textarea_rows' => 10,
				) ); ?>
                <p class="description" id="planaday-api-bedankttekst">
                    Tekst die op de pagina wordt getoond als een boeking door een deelnemer is gelukt<br/>
                    Velden met {veld} worden vervangen door waardes:<br/>
                <ul>
                    <li>{cursus} = Naam van cursus</li>
                    <li>{cursuscode} = (PAD)Code van cursus</li>
                    <li>{startdatum} = Startdatum van cursus</li>
                    <li>{naam} = Voornaam, intialen, tussenvoegsel en achternaam</li>
                    <li>{voornaam} = Voornaam</li>
                    <li>{achternaam} = Achternaam</li>
                    <li>{website} = Titel/naam van deze website</li>
                    <li>{padboekingid} = BoekingID vanuit Planaday als boeking is gelukt</li>
                    <li>{idealtransactieid} = TransactieID van iDeal <strong>indien betalingen actief</strong></li>
                    <li>{betaald} = Melding of betaling is gelukt <strong>indien betalingen actief</strong></li>
                    <li>{bedrag} = Afgerekende bedrag <strong>indien betalingen actief</strong></li>
                </ul>
                <br/>
                <i><strong>Tip: gebruik hier geen plugins of shortcodes oid.</strong></i>
                </p>
            </td>
        </tr>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f; text-decoration: underline;">Boeking niet gelukt!</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Na boeking redirect?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[bedanktredirectmislukt]"
                       value="1" <?php
				if ( ! isset( $options['bedanktredirectmislukt'] ) || $options['bedanktredirectmislukt'] === '' ) {
					$bedanktredirectmislukt = "1";
				} else {
					$bedanktredirectmislukt = $options['bedanktredirectmislukt'];
				}
				if ( $bedanktredirectmislukt === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[bedanktredirectmislukt]"
                       value="0" <?php if ( $bedanktredirectmislukt === '0' || $bedanktredirectmislukt === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-bedanktredirectmislukt">
                    Wil je dat na het boeken (welke is mislukt om welke reden dan ook) - indien je een pagina hieronder hebt gekozen - dat
                    er een redirect plaatsvind?<br/><br/>
                    <strong>Let op: </strong> enkel van toepassing als je hierboven een pagina hebt gekozen!<br/>
                    <strong>Belangrijk: </strong> kies je voor redirect, dan kunnen 'waardes' zoals cursusnaam en naam deelnemer niet
                    getoond worden
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Selecteer redirect pagina
            </th>
            <td>
                <select name='planaday-api-general[bedankurlmislukt]'>
                    <option value='0'><?php _e( 'Selecteer een pagina (geen)', 'planaday-api' ); ?></option>
					<?php $pages = get_pages(); ?>
					<?php foreach ( $pages as $page ) { ?>
                        <option value='<?php echo $page->ID; ?>' <?php selected( $options['bedankurlmislukt'],
							$page->ID ); ?> ><?php echo $page->post_title; ?></option>
					<?php }; ?>
                </select>
                <p class="description" id="planaday-api-general-bedankurlmislukt">
                    Kies enkel een pagina als je een aparte 'mislukt' pagina hebt gemaakt.<br/><br/>
                    <strong>-- of --</strong><br/><br/>
                    Vul hieronder een "Bedankt tekst na boeking" in:
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Bedankt tekst na boeking
            </th>
            <td>
				<?php
				if ( ! isset( $options['bedankttekstmislukt'] ) || $options['bedankttekstmislukt'] === '' ) {
					$mailbedankttekst = "Hallo {naam},\n\n";
					$mailbedankttekst .= "Bedankt voor je aanmelding voor de cursus: {cursus} met startdatum: {startdatum}.\n";
					$mailbedankttekst .= "Om een of andere reden is de boeking niet gelukt.\n\n";
					$mailbedankttekst .= "Neem voor de zekerheid contact met ons op zodat wij dit kunnen nakijken.\n\n";
				} else {
					$mailbedankttekst = $options['bedankttekstmislukt'];
				}
				wp_editor( $mailbedankttekst, 'bedankttekstmislukt', array(
					'textarea_name' => 'planaday-api-general[bedankttekstmislukt]',
					'textarea_rows' => 10,
				) ); ?>
                <p class="description" id="planaday-api-bedankttekstmislukt">
                    Tekst die op de pagina wordt getoond als een boeking door een deelnemer niet is gelukt<br/>
                    Velden met {veld} worden vervangen door waardes:<br/>
                <ul>
                    <li>{cursus} = Naam van cursus</li>
                    <li>{cursuscode} = (PAD)Code van cursus</li>
                    <li>{startdatum} = Startdatum van cursus</li>
                    <li>{naam} = Voor en achternaam</li>
                    <li>{voornaam} = Voornaam</li>
                    <li>{achternaam} = Achternaam</li>
                    <li>{website} = Titel/naam van deze website</li>
                </ul>
                <br/>
                <i><strong>Tip: gebruik hier geen plugins of shortcodes oid.</strong></i>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Emailadres bij boeking
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[mailcursusaanmelding]'
                       value='<?php echo $options['mailcursusaanmelding']; ?>'/>
                <p class="description" id="planaday-api-mailcursusaanmelding">
                    Vul E-mailadres in van waar jij eventueel aanmeldingen op wil ontvangen.<br/>
                    Ook als een boeking door wat dan ook niet lukt, krijg je als opleider ook mail met alle details.<br/>
                    <strong>Let op: </strong>Laat deze leeg als je deze niet wil ontvangen.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Tekst die je toont bij 'dagdelen'
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[dagdelentekst]'
                       value='<?php
				       if ( $options['dagdelentekst'] === "" ) {
					       echo "dagen";
				       } else {
					       echo $options['dagdelentekst'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-dagdelentekst">
                    Geef zelf aan welke tekst getoond moet worden. Bijvoorbeeld 'dagdelen' of 'dagen'
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon omschrijving van cursus
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonomschrijvingcursus]"
                       value="1" <?php if ( $options['toonomschrijvingcursus'] === '1' || $options['toonomschrijvingcursus'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonomschrijvingcursus]"
                       value="0" <?php if ( $options['toonomschrijvingcursus'] === '0' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonomschrijvingcursus">
                    Wil je de omschrijving van de cursus tonen in overzicht en op detailpagina?<br>
                    Je kunt bij 'overzicht' nog een limiet toevoegen van aantal tekens.
                </p>
            </td>
        </tr>


        <tr>
            <th scope="row" class="titelrij">
                Toon debug-info
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toondebuginfo]"
                       value="1" <?php if ( $options['toondebuginfo'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toondebuginfo]"
                       value="0" <?php if ( $options['toondebuginfo'] === '0' || $options['toondebuginfo'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toondebuginfo">
                    Toon alle (api) calls en meer debug informatie.
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Betalingen actief
            </th>
            <td>
                <input type="radio" name="planaday-api-general[betalingenactief]"
                       value="1" <?php if ( $options['betalingenactief'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[betalingenactief]"
                       value="0" <?php if ( $options['betalingenactief'] === '0' || $options['betalingenactief'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-betalingenactief">
                    Moet betalingen (via iDeal) actief zijn? <br/>
                    Als je deze actief hebt, dan komt er een extra menu beschikbaar waarin je instellingen moet
                    wijzigen.
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Opties meetellen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[optiesmeetellen]"
                       value="1" <?php
				if ( ! isset( $options['optiesmeetellen'] ) || $options['optiesmeetellen'] === '' ) {
					$optiesmeetellen = "1";
				} else {
					$optiesmeetellen = $options['optiesmeetellen'];
				}
				if ( $optiesmeetellen === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[optiesmeetellen]"
                       value="0" <?php if ( $optiesmeetellen === '0' || $optiesmeetellen === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-optiesmeetellen">
                    Moeten openstaande opties (van inschrijvingen) ook meegeteld worden in beschikbaarheid?<br/>
                    Indien ja, dan worden opties ook als 'bezet' meegeteld.
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Toon prijs exclusief BTW?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[btwinofexbtwtonen]"
                       value="1" <?php
				if ( ! isset( $options['btwinofexbtwtonen'] ) || $options['btwinofexbtwtonen'] === '' ) {
					$btwinofexbtwtonen = "1";
				} else {
					$btwinofexbtwtonen = $options['btwinofexbtwtonen'];
				}
				if ( $btwinofexbtwtonen === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[btwinofexbtwtonen]"
                       value="0" <?php if ( $btwinofexbtwtonen === '0' || $btwinofexbtwtonen === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-btwinofexbtwtonen">
                    Wil je dat de prijs die vanuit Planaday komt standaard exclusief BTW wordt getoond?<br/>
                    Standaard staat deze op nee, dus inclusief BTW.
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Toon incl/ex BTW bij prijs?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[btwinofexbtwtonenlabel]"
                       value="1" <?php
				if ( ! isset( $options['btwinofexbtwtonenlabel'] ) || $options['btwinofexbtwtonenlabel'] === '' ) {
					$btwinofexbtwtonenlabel = "1";
				} else {
					$btwinofexbtwtonenlabel = $options['btwinofexbtwtonenlabel'];
				}
				if ( $btwinofexbtwtonenlabel === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[btwinofexbtwtonenlabel]"
                       value="0" <?php if ( $btwinofexbtwtonenlabel === '0' || $btwinofexbtwtonenlabel === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-btwinofexbtwtonenlabel">
                    Wil je melding in/ex BTW bij de prijs?.
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Cursussen enkel met </br>E-learning overslaan?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[skipcoursewithonlyelearning]"
                       value="1" <?php
				if ( ! isset( $options['skipcoursewithonlyelearning'] ) || $options['skipcoursewithonlyelearning'] === '' ) {
					$skipcoursewithonlyelearning = "1";
				} else {
					$skipcoursewithonlyelearning = $options['skipcoursewithonlyelearning'];
				}
				if ( $skipcoursewithonlyelearning === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[skipcoursewithonlyelearning]"
                       value="0" <?php if ( $skipcoursewithonlyelearning === '0' || $skipcoursewithonlyelearning === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-skipcoursewithonlyelearning">
                    Moeten de cursussen met enkel E-learning worden overgeslagen?
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_overzicht_settings( $options ) {
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Cursus overzicht instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon code95 icoon
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncode95overzicht]"
                       value="1" <?php if ( $options['tooncode95overzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[tooncode95overzicht]"
                       value="0" <?php if ( $options['tooncode95overzicht'] === '0' || $options['tooncode95overzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-tooncode95overzicht">
                    Wil je laten zien dat de cursus code95 bevat? <br/>Let op: dit kan ook tekstueel in
                    omschrijving!
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon soob icoon
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonsooboverzicht]"
                       value="1" <?php if ( $options['toonsooboverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonsooboverzicht]"
                       value="0" <?php if ( $options['toonsooboverzicht'] === '0' || $options['toonsooboverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonsooboverzicht">
                    Wil je laten zien dat de cursus subsidie in kader van SOOB bevat? <br/>Let op: dit kan ook
                    tekstueel in omschrijving!
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon E-learning in je overzicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonelearningdagdeelinoverzicht]"
                       value="1" <?php if ( $options['toonelearningdagdeelinoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonelearningdagdeelinoverzicht]"
                       value="0" <?php if ( $options['toonelearningdagdeelinoverzicht'] === '0' || $options['toonelearningdagdeelinoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonelearningdagdeelinoverzicht">
                    Wil je dagdelen met E-learning tonen in je overzicht bij een cursus?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon dagdelen lijstje
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toondetaildagdelenlijstinoverzicht]"
                       value="1" <?php if ( $options['toondetaildagdelenlijstinoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toondetaildagdelenlijstinoverzicht]"
                       value="0" <?php if ( $options['toondetaildagdelenlijstinoverzicht'] === '0' || $options['toondetaildagdelenlijstinoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toondetaildagdelenlijstinoverzicht">
                    Wil je een lijstje tonen met alle dagdelen (inclusief de tijden)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon locatie bij dagdeel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlocatiedagdeeloverzicht]"
                       value="1" <?php if ( $options['toonlocatiedagdeeloverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonlocatiedagdeeloverzicht]"
                       value="0" <?php if ( $options['toonlocatiedagdeeloverzicht'] === '0' || $options['toonlocatiedagdeeloverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonlocatiedagdeeloverzicht">
                    Wil je bij ieder dagdeel die wordt getoond ook locatie (stad) tonen (kan enkel als toon dagdelen
                    op 'ja' staat)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Label per dagdeel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlabelindagdeel]"
                       value="1" <?php if ( $options['toonlabelindagdeel'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonlabelindagdeel]"
                       value="0" <?php if ( $options['toonlabelindagdeel'] === '0' || $options['toonlabelindagdeel'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonlabelindagdeel">
                    Wil je labels per dagdeel tonen? Kan enkel als je dagdelen lijstje laat zien!
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon financiele toelichting
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncostsremarkoverzicht]"
                       value="1" <?php if ( $options['tooncostsremarkoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncostsremarkoverzicht]"
                       value="0" <?php if ( $options['tooncostsremarkoverzicht'] === '0' || $options['tooncostsremarkoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncostsremarkoverzicht">
                    In Planaday kun je een toelichting geven op de kosten. Deze kun je optioneel tonen in overzicht
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon elearning icon
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncursuselearning]"
                       value="1" <?php if ( $options['tooncursuselearning'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncursuselearning]"
                       value="0" <?php if ( $options['tooncursuselearning'] === '0' || $options['tooncursuselearning'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncursuselearning">
                    Wil je dat zichtbaar is dat één van de dagdelen elearning bevat?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon eventuele labels
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncursuslabelsoverzicht]"
                       value="1" <?php if ( $options['tooncursuslabelsoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncursuslabelsoverzicht]"
                       value="0" <?php if ( $options['tooncursuslabelsoverzicht'] === '0' || $options['tooncursuslabelsoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncursuslabelsoverzicht">
                    Labels van cursus tonen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon dagdelen aantal
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toondagdelen]"
                       value="1" <?php if ( $options['toondagdelen'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toondagdelen]"
                       value="0" <?php if ( $options['toondagdelen'] === '0' || $options['toondagdelen'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toondagdelen">
                    Wil je aantal dagdelen van die cursus laten zien in de overzichten?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Locatie tonen
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlocatiebijoverzicht]"
                       value="1" <?php if ( $options['toonlocatiebijoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonlocatiebijoverzicht]"
                       value="0" <?php if ( $options['toonlocatiebijoverzicht'] === '0' || $options['toonlocatiebijoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonlocatiebijoverzicht">
                    Wil je (eerste) locatie tonen van de cursus?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon prijs
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonprijs]"
                       value="1" <?php if ( $options['toonprijs'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonprijs]"
                       value="0" <?php if ( $options['toonprijs'] === '0' || $options['toonprijs'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toonprijs">
                    Toon de prijs op de overzichtpagina's
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon volle cursus
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonvollecursus]"
                       value="1" <?php if ( $options['toonvollecursus'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonvollecursus]"
                       value="0" <?php if ( $options['toonvollecursus'] === '0' || $options['toonvollecursus'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toonvollecursus">
                    Cursus ook laten zien als deze is volgeboekt?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Cursus & dagdelen in verleden?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncursusdagdelenverleden]"
                       value="1" <?php if ( $options['tooncursusdagdelenverleden'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncursusdagdelenverleden]"
                       value="0" <?php if ( $options['tooncursusdagdelenverleden'] === '0' || $options['tooncursusdagdelenverleden'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncursusdagdelenverleden">
                    Wil je de cursus laten zien als tenminste één van de dagdelen in het verleden liggen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon button bij cursus
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonbutton]"
                       value="1" <?php if ( $options['toonbutton'] == '1' || $options['toonbutton'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonbutton]"
                       value="0" <?php if ( $options['toonbutton'] === '0' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toonbutton">
                    Wil je een button tonen in overzicht bij cursus?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon 'startgarantie'
            </th>
            <td>
                <input type="radio" name="planaday-api-general[startgarantieoverzicht]"
                       value="1" <?php if ( $options['startgarantieoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[startgarantieoverzicht]"
                       value="0" <?php if ( $options['startgarantieoverzicht'] === '0' || $options['startgarantieoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-startgarantieoverzicht">
                    Indien actief wordt er een label getoond naast startdatum met 'startgarantie' indien aangegeven
                    in Planaday
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon 'geld terug'
            </th>
            <td>
                <input type="radio" name="planaday-api-general[geldterugoverzicht]"
                       value="1" <?php if ( $options['geldterugoverzicht'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[geldterugoverzicht]"
                       value="0" <?php if ( $options['geldterugoverzicht'] === '0' || $options['geldterugoverzicht'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-geldterugoverzicht">
                    Indien actief wordt er een label getoond bij prijs met 'geld terug garantie' indien aangegeven
                    in Planaday
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Toon afbeelding
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonimginoverzicht]"
                       value="1" <?php
				if ( ! isset( $options['toonimginoverzicht'] ) || $options['toonimginoverzicht'] === '' ) {
					$toonimginoverzicht = "0";
				} else {
					$toonimginoverzicht = $options['toonimginoverzicht'];
				}

				if ( $toonimginoverzicht === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonimginoverzicht]"
                       value="0" <?php if ( $toonimginoverzicht === '0' || $toonimginoverzicht === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonimginoverzicht">
                    Wil je eerste afbeelding vanuit cursus tonen indien aanwezig?<br>
                    <strong>Let op: </strong>enkel de eerste afbeelding bij 'bestanden' in je cursus(sjabloon) met de naam 'wordpress' wordt
                    getoond
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Limiet tekst
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[limiettekstomschrijving]'
                       value='<?php
				       if ( $options['limiettekstomschrijving'] === "" ) {
					       echo "200";
				       } else {
					       echo $options['limiettekstomschrijving'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-limiettekstomschrijving">
                    Voorbeeld: 75.<br>Limiet van tekst als deze wordt getoond (in overzichten) indien deze bij 'Algemeen' op 'ja' staat<br>
                    Als je afbeeldingen IN je tekst/omschrijving gebruikt, stel dan limiet in op bijvoorbeeld 500000.<br>
                    Zet deze op 0 als je GEEN omschrijving in overzichten wil tonen.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Tekst geen cursussen
            </th>
            <td>
				<?php
				if ( ! isset( $options['tekstgeencursussen'] ) || $options['tekstgeencursussen'] === '' ) {
					$tekstgeencursussen = "Momenteel zijn er geen cursussen gevonden, bel naar ons voor meer informatie. Of vul dit \n\n";
				} else {
					$tekstgeencursussen = $options['tekstgeencursussen'];
				}
				wp_editor( $tekstgeencursussen, 'tekstgeencursussen', array(
					'textarea_name' => 'planaday-api-general[tekstgeencursussen]',
					'textarea_rows' => 10,
				) ); ?>
                <p class="description" id="planaday-api-tekstgeencursussen">
                    Tekst indien er geen cursussen zijn van gekozen template (of geheel geen cursussen)<br/>
                    Voorbeeld: <br>Momenteel zijn er geen cursussen gevonden, bel naar 085-8722222 voor meer
                    informatie.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Tekst in button
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[buttontekstoverzicht]'
                       value='<?php
				       if ( $options['buttontekstoverzicht'] === "" ) {
					       echo "Bekijk cursus";
				       } else {
					       echo $options['buttontekstoverzicht'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-buttontekstoverzicht">
                    De tekst die in de knop zit om de cursus te bekijken
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_zoeken_settings( $options ) {
		?>

        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Zoek instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Tekstveld opnemen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toontextbijsearch]"
                       value="1" <?php
				if ( ! isset( $options['toontextbijsearch'] ) || $options['toontextbijsearch'] === '' ) {
					$toontextbijsearch = "1";
				} else {
					$toontextbijsearch = $options['toontextbijsearch'];
				}

				if ( $toontextbijsearch === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toontextbijsearch]"
                       value="0" <?php if ( $toontextbijsearch === '0' || $toontextbijsearch === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toontextbijsearch">
                    Wil je dat er een textveld is om te kunnen zoeken in titels?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Locaties in zoekformulier?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlocatiesbijsearch]"
                       value="1" <?php if ( $options['toonlocatiesbijsearch'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonlocatiesbijsearch]"
                       value="0" <?php if ( $options['toonlocatiesbijsearch'] === '0' || $options['toonlocatiesbijsearch'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toonlocatiesbijsearch">
                    Wil je dat je kunt zoeken op locatie (enkel de stad wordt getoond indien gevuld).
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Zoeken op labels?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlabelsbijsearch]"
                       value="1" <?php
				if ( ! isset( $options['toonlabelsbijsearch'] ) || $options['toonlabelsbijsearch'] === '' ) {
					$toonlabelsbijsearch = "0";
				} else {
					$toonlabelsbijsearch = $options['toonlabelsbijsearch'];
				}

				if ( $toonlabelsbijsearch === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonlabelsbijsearch]"
                       value="0" <?php if ( $toonlabelsbijsearch === '0' || $toonlabelsbijsearch === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonlabelsbijsearch">
                    Wil je dat er labels geselecteerd kunnen worden bij zoeken?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Optie code95?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncode95bijsearch]"
                       value="1" <?php
				if ( ! isset( $options['tooncode95bijsearch'] ) || $options['tooncode95bijsearch'] === '' ) {
					$tooncode95bijsearch = "0";
				} else {
					$tooncode95bijsearch = $options['tooncode95bijsearch'];
				}

				if ( $tooncode95bijsearch === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[tooncode95bijsearch]"
                       value="0" <?php if ( $tooncode95bijsearch === '0' || $tooncode95bijsearch === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-tooncode95bijsearch">
                    Wil je dat gekozen en gezocht kan worden op code95?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Optie soob?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonsoobbijsearch]"
                       value="1" <?php
				if ( ! isset( $options['toonsoobbijsearch'] ) || $options['toonsoobbijsearch'] === '' ) {
					$toonsoobbijsearch = "0";
				} else {
					$toonsoobbijsearch = $options['toonsoobbijsearch'];
				}

				if ( $toonsoobbijsearch === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonsoobbijsearch]"
                       value="0" <?php if ( $toonsoobbijsearch === '0' || $toonsoobbijsearch === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonsoobbijsearch">
                    Wil je dat gekozen en gezocht kan worden op soob?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Optie E-learning?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonelearningbijsearch]"
                       value="1" <?php
				if ( ! isset( $options['toonelearningbijsearch'] ) || $options['toonelearningbijsearch'] === '' ) {
					$toonelearningbijsearch = "0";
				} else {
					$toonelearningbijsearch = $options['toonelearningbijsearch'];
				}
				if ( $toonelearningbijsearch === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonelearningbijsearch]"
                       value="0" <?php if ( $toonelearningbijsearch === '0' || $toonelearningbijsearch === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonelearningbijsearch">
                    Wil je dat gekozen en gezocht kan worden op E-learning?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Resultaten in blokvorm?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooninblockvorm]"
                       value="1" <?php
				if ( ! isset( $options['tooninblockvorm'] ) || $options['tooninblockvorm'] === '' ) {
					$tooninblockvorm = "1";
				} else {
					$tooninblockvorm = $options['tooninblockvorm'];
				}
				if ( $tooninblockvorm === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[tooninblockvorm]"
                       value="0" <?php if ( $tooninblockvorm === '0' || $tooninblockvorm === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-tooninblockvorm">
                    Moeten resultaten in blokvorm (ja) of in een table (nee) worden getoond?
                </p>
            </td>
        </tr>

		<?php
	}

	public function planaday_api_detail_settings( $options ) {
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Cursus detail instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                SOOB subsidie
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonsoobdetailcursus]"
                       value="1" <?php if ( $options['toonsoobdetailcursus'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonsoobdetailcursus]"
                       value="0" <?php if ( $options['toonsoobdetailcursus'] === '0' || $options['toonsoobdetailcursus'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonsoobdetailcursus">
                    Moet op detailpagina SOOB subsidie getoond worden indien bij cursus deze ook aan staat?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Code95
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncode95cursus]"
                       value="1" <?php if ( $options['tooncode95cursus'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[tooncode95cursus]"
                       value="0" <?php if ( $options['tooncode95cursus'] === '0' || $options['tooncode95cursus'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-tooncode95cursus">
                    Moet op detailpagina Code95 getoond worden indien bij cursus deze ook aanwezig is?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon eventuele labels
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncursuslabelsdetail]"
                       value="1" <?php if ( $options['tooncursuslabelsdetail'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncursuslabelsdetail]"
                       value="0" <?php if ( $options['tooncursuslabelsdetail'] === '0' || $options['tooncursuslabelsdetail'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncursuslabelsdetail">
                    Labels van cursus tonen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon dagdelen aantal
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toondetaildagdelen]"
                       value="1" <?php if ( $options['toondetaildagdelen'] === '1' || $options['toondetaildagdelen'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toondetaildagdelen]"
                       value="0" <?php if ( $options['toondetaildagdelen'] === '0' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toondetaildagdelen">
                    Wil je de tekst 'Deze cursus bestaat uit x dagdelen' tonen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon dagdelen lijstje
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toondetaildagdelenlijst]"
                       value="1" <?php if ( $options['toondetaildagdelenlijst'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toondetaildagdelenlijst]"
                       value="0" <?php if ( $options['toondetaildagdelenlijst'] === '0' || $options['toondetaildagdelenlijst'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toondetaildagdelenlijst">
                    Wil je een lijstje tonen met alle dagdelen (inclusief de tijden)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon locatie bij dagdeel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonlocatiedagdeel]"
                       value="1" <?php if ( $options['toonlocatiedagdeel'] === '1' || $options['toonlocatiedagdeel'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonlocatiedagdeel]"
                       value="0" <?php if ( $options['toonlocatiedagdeel'] === '0' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonlocatiedagdeel">
                    Wil je bij ieder dagdeel die wordt getoond ook locatie (stad) tonen (kan enkel als toon dagdelen
                    op 'ja' staat)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon elearning bij dagdeel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonelearningdagdeel]"
                       value="1" <?php if ( $options['toonelearningdagdeel'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonelearningdagdeel]"
                       value="0" <?php if ( $options['toonelearningdagdeel'] === '0' || $options['toonelearningdagdeel'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonelearningdagdeel">
                    Wil je bij ieder dagdeel die elearning is een icoon wordt getoond (kan enkel als toon dagdelen
                    op 'ja' staat)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon niveau cursus
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonniveau]"
                       value="1" <?php
				if ( ! isset( $options['toonniveau'] ) || $options['toonniveau'] === '' ) {
					$toonniveau = "0";
				} else {
					$toonniveau = $options['toonniveau'];
				}

				if ( $toonniveau === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonniveau]"
                       value="0" <?php if ( $toonniveau === '0' || $toonniveau === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonniveau">
                    Wil je het niveau van de cursus tonen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon omschrijving dagdeel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonomschrijvingdagdeel]"
                       value="1" <?php
				if ( ! isset( $options['toonomschrijvingdagdeel'] ) || $options['toonomschrijvingdagdeel'] === '' ) {
					$toonomschrijvingdagdeel = "0";
				} else {
					$toonomschrijvingdagdeel = $options['toonomschrijvingdagdeel'];
				}

				if ( $toonomschrijvingdagdeel === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonomschrijvingdagdeel]"
                       value="0" <?php if ( $toonomschrijvingdagdeel === '0' || $toonomschrijvingdagdeel === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonomschrijvingdagdeel">
                    Wil je omschrijving van dagdeel tonen na een klik?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon beschikbaarheid
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonbeschikbareplaatsen]"
                       value="1" <?php if ( $options['toonbeschikbareplaatsen'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonbeschikbareplaatsen]"
                       value="0" <?php if ( $options['toonbeschikbareplaatsen'] === '0' || $options['toonbeschikbareplaatsen'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonbeschikbareplaatsen">
                    Wil je de nog beschikbare plaatsen tonen indien de cursus nog niet vol is?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon 'startgarantie'
            </th>
            <td>
                <input type="radio" name="planaday-api-general[startgarantiedetail]"
                       value="1" <?php if ( $options['startgarantiedetail'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[startgarantiedetail]"
                       value="0" <?php if ( $options['startgarantiedetail'] === '0' || $options['startgarantiedetail'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-startgarantiedetail">
                    Indien actief wordt er een label getoond naast startdatum
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon 'geld terug garantie'
            </th>
            <td>
                <input type="radio" name="planaday-api-general[geldterugdetail]"
                       value="1" <?php if ( $options['geldterugdetail'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[geldterugdetail]"
                       value="0" <?php if ( $options['geldterugdetail'] === '0' || $options['geldterugdetail'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-geldterugdetail">
                    Indien actief wordt er een label getoond met 'geld terug garantie'
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon financiele toelichting
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncostsremark]"
                       value="1" <?php if ( $options['tooncostsremark'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[tooncostsremark]"
                       value="0" <?php if ( $options['tooncostsremark'] === '0' || $options['tooncostsremark'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-tooncostsremark">
                    In Planaday kun je een toelichting geven op de kosten. Deze kun je optioneel tonen
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon prijs
                <span style="text-align: left; color: #ff7700;"><b>Nieuw!</b></span>
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonprijsdetailpagina]"
                       value="1" <?php if ( $options['toonprijsdetailpagina'] === '1' || $options['toonprijsdetailpagina'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[toonprijsdetailpagina]"
                       value="0" <?php if ( $options['toonprijsdetailpagina'] === '0' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-toonprijsdetailpagina">
                    Wil je de prijs tonen op detailpagina? <br/>Standaard = 'ja'
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                E-learning meetellen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[countelearningasdaypart]"
                       value="1" <?php if ( $options['countelearningasdaypart'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[countelearningasdaypart]"
                       value="0" <?php if ( $options['countelearningasdaypart'] === '0' || $options['countelearningasdaypart'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-countelearningasdaypart">
                    Moet E-learning meegeteld worden als 'dagdeel'?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon afbeelding
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonimgindetail]"
                       value="1" <?php
				if ( ! isset( $options['toonimgindetail'] ) || $options['toonimgindetail'] === '' ) {
					$toonimgindetail = "0";
				} else {
					$toonimgindetail = $options['toonimgindetail'];
				}

				if ( $toonimgindetail === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonimgindetail]"
                       value="0" <?php if ( $toonimgindetail === '0' || $toonimgindetail === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonimgindetail">
                    Wil je eerste afbeelding vanuit cursus tonen indien aanwezig?<br>
                    <strong>Let op: </strong>enkel de eerste afbeelding bij 'bestanden' in je cursus(sjabloon) met de naam 'wordpress' wordt
                    getoond
                </p>
            </td>
        </tr>

		<?php
	}

	public function planaday_api_kalender_settings( $options ) {
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Cursus kalender instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Kleur van achtergrond
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[calendarcolorback]'
                       value='<?php echo $options['calendarcolorback']; ?>'/>
                <p class="description" id="planaday-api-calendarcolorback">
                    Vul deze waarde in als HEX. Bijvoorbeeld: #cccccc
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Kleur van tekst
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[calendarcolortext]'
                       value='<?php echo $options['calendarcolortext']; ?>'/>
                <p class="description" id="planaday-api-calendarcolortext">
                    Vul deze waarde in als HEX. Bijvoorbeeld: #ffffff
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toon tekst bij een mouseover
            </th>
            <td>
                <input type="radio" name="planaday-api-general[tooncalendarmouseover]"
                       value="1" <?php if ( $options['tooncalendarmouseover'] === '1' || $options['tooncalendarmouseover'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[tooncalendarmouseover]"
                       value="0" <?php if ( $options['tooncalendarmouseover'] === '0' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-tooncalendarmouseover">
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_materiaal_settings( $options ) {
		?>

        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Materialen bij boeking bijbestellen</h3>
                <p>Indien er materialen in een dagdeel bij een cursus gekoppeld zijn en beschikbaar zijn voor de
                    api,<br/>
                    dan kun je deze hieronder activeren zodat bezoekers deze meteen kunnen meebestellen.</p>
            </th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Materialen bij cursus?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[materialbookingactive]"
                       value="1" <?php if ( $options['materialbookingactive'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[materialbookingactive]"
                       value="0" <?php if ( $options['materialbookingactive'] === '0' || $options['materialbookingactive'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-materialbookingactive">
                    Wil je de gekoppelde materialen bij cursus/dagdelen zichtbaar hebben en bestelbaar maken bij een
                    booking?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Titel bij materialen
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[materialtitle]'
                       value='<?php
				       if ( $options['materialtitle'] === "" ) {
					       echo "Welke opties zou je willen meebestellen?";
				       } else {
					       echo $options['materialtitle'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-materialtitle">
                    Indien actief, welke titel moet getoond worden
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Toelichting bij materialen
            </th>
            <td>
				<?php wp_editor( $options['materialtext'], 'materialtext', array(
					'textarea_name' => 'planaday-api-general[materialtext]',
					'textarea_rows' => 6,
				) ); ?>
                <p class="description" id="planaday-api-materialtext">
                    Toelichting indien materialen geboekt kunnen worden actief is<br/>
                    <i><strong>Tip: gebruik hier geen plugins of shortcodes oid.</strong></i>
                </p>
            </td>
        </tr>
		<?php
	}

	public function planaday_api_formulier_settings( $options ) {
		?>
        <tr>
            <th colspan="2"><h3 style="color: #ca4a1f;">Boekingformulier instellingen</h3></th>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Cursist mailen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[mailcursusaanmeldingcursist]"
                       value="1" <?php if ( $options['mailcursusaanmeldingcursist'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[mailcursusaanmeldingcursist]"
                       value="0" <?php if ( $options['mailcursusaanmeldingcursist'] === '0' || $options['mailcursusaanmeldingcursist'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-mailcursusaanmeldingcursist">
                    Moet cursist mail krijgen dat aanmelding wel/niet is gelukt?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Mail tekst naar cursist
            </th>
            <td>
				<?php
				if ( ! isset( $options['mailbedankttekst'] ) || $options['mailbedankttekst'] === '' ) {
					$mailbedankttekst = "Hallo {naam},\n\n";
					$mailbedankttekst .= "Er is een aanmelding binnengekomen voor de cursus: {cursus} met startdatum: {startdatum}.\n";
					$mailbedankttekst .= "Deze aanmelding voor de cursus/opleiding is bij ons binnengekomen en moet nog worden verwerkt.\n";
					$mailbedankttekst .= "Zodra wij deze hebben verwerkt krijg je nog een definitieve bevestiging per E-mail.\n\n";
					$mailbedankttekst .= "Met vriendelijke groet,\n\n-- \n{website}\n";
				} else {
					$mailbedankttekst = $options['mailbedankttekst'];
				}
				wp_editor( $mailbedankttekst, 'mailbedankttekst', array(
					'textarea_name' => 'planaday-api-general[mailbedankttekst]',
					'textarea_rows' => 6,
				) ); ?>
                <p class="description" id="planaday-api-mailbedankttekst">
                    Mail die wordt gestuurd na het boeken, indien hierboven op 'ja' staat.<br/>
                    Velden met {veld} worden vervangen door waardes:<br/>
                <ul>
                    <li>{cursus} = Naam van cursus</li>
                    <li>{cursuscode} = (PAD)Code van cursus</li>
                    <li>{startdatum} = Startdatum van cursus</li>
                    <li>{naam} = Voor en achternaam</li>
                    <li>{voornaam} = Voornaam</li>
                    <li>{achternaam} = Achternaam</li>
                    <li>{website} = Titel/naam van deze website</li>
                    <li>{idealtransactieid} = TransactieID van iDeal indien betalingen actief</li>
                    <li>{padboekingid} = BoekingID vanuit Planaday als boeking is gelukt</li>
                    <li>{betaald} = Melding of betaling is gelukt</li>
                    <li>{bedrag} = Afgerekende bedrag</li>
                </ul>
                <br/>
                <i><strong>Tip: gebruik hier geen plugins of shortcodes oid.</strong></i>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Bedrijf mailen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[mailcursusaanmeldingbedrijf]"
                       value="1" <?php if ( $options['mailcursusaanmeldingbedrijf'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[mailcursusaanmeldingbedrijf]"
                       value="0" <?php if ( $options['mailcursusaanmeldingbedrijf'] === '0' || $options['mailcursusaanmeldingbedrijf'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-mailcursusaanmeldingbedrijf">
                    Moet bedrijf mail krijgen dat aanmelding wel/niet is gelukt?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Mail tekst naar bedrijf
            </th>
            <td>
				<?php
				if ( ! isset( $options['mailbedankttekstbedrijf'] ) || $options['mailbedankttekstbedrijf'] === '' ) {
				} else {
					$mailbedankttekstbedrijf = $options['mailbedankttekstbedrijf'];
				}
				wp_editor( $mailbedankttekstbedrijf, 'mailbedankttekstbedrijf', array(
					'textarea_name' => 'planaday-api-general[mailbedankttekstbedrijf]',
					'textarea_rows' => 6,
				) ); ?>
                <p class="description" id="planaday-api-mailbedankttekstbedrijf">
                    Mail die wordt gestuurd na het boeken, indien hierboven op 'ja' staat.<br/>
                    Velden met {veld} worden vervangen door waardes:<br/>
                <ul>
                    <li>{cursus} = Naam van cursus</li>
                    <li>{cursuscode} = (PAD)Code van cursus</li>
                    <li>{startdatum} = Startdatum van cursus</li>
                    <li>{bedrijfsnaam} = Bedrijfsnaam</li>
                    <li>{studenteninfo} = Lijst met aangemelde cursisten</li>
                    <li>{betaalinfo} = Informatie over betaling</li>
                    <li>{website} = Titel/naam van deze website</li>
                    <li>{padboekingid} = BoekingID vanuit Planaday als boeking is gelukt</li>
                    <li>{betaald} = Melding of betaling is gelukt</li>
                    <li>{bedrag} = Afgerekende bedrag</li>
                </ul>
                <br/>
                <i><strong>Tip: gebruik hier geen plugins of shortcodes oid.</strong></i>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Enkel particulier?
                <span style="text-align: left; color: #ff7700;"><b>Nieuw!</b></span>
            </th>
            <td>
                <input type="radio" name="planaday-api-general[onlybookingelparticulier]"
                       value="1" <?php if ( $options['onlybookingelparticulier'] === '1' || $options['onlybookingelparticulier'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[onlybookingelparticulier]"
                       value="0" <?php if ( $options['onlybookingelparticulier'] === '0' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-onlybookingelparticulier">
                    Mogen enkel particulieren boeken? Standaard nee<br>
                    <strong>Toelichting: </strong>Dit betekend dat een bedrijf niet zich kan aanmelden (met cursisten).
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Particulier als voorkeur?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[voorkeurbooleancompany]"
                       value="1" <?php if ( $options['voorkeurbooleancompany'] === '1' || $options['voorkeurbooleancompany'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[voorkeurbooleancompany]"
                       value="0" <?php if ( $options['voorkeurbooleancompany'] === '0' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-voorkeurbooleancompany">
                    Moet particulier (standaard = ja) als voorkeur gekozen zijn in keuze bedrijf/particulier?<br/>
                    <strong>Let op: </strong> dit enkel mogelijk als 'Enkel particulier' op 'nee' staat.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Adres bij cursist uitvragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagadrescursist]"
                       value="1" <?php if ( $options['vraagadrescursist'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagadrescursist]"
                       value="0" <?php if ( $options['vraagadrescursist'] === '0' || $options['vraagadrescursist'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagadrescursist">
                    <strong>Let op:</strong> Als deze actief is, zijn deze ook verplicht voor de deelnemer om in te vullen
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Huisnummer extensie vragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraaghuisnrext]"
                       value="1" <?php if ( $options['vraaghuisnrext'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraaghuisnrext]"
                       value="0" <?php if ( $options['vraaghuisnrext'] === '0' || $options['vraaghuisnrext'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraaghuisnrext">

                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Contactpersoon vragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagcontactpersoon]"
                       value="1" <?php if ( $options['vraagcontactpersoon'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagcontactpersoon]"
                       value="0" <?php if ( $options['vraagcontactpersoon'] === '0' || $options['vraagcontactpersoon'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagcontactpersoon">
                    Wil je dat er de optie 'contactpersoon' wordt gevraagd zodat deze als contactpersoon in Planaday
                    komt te staan?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Roepnaam uitvragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagroepnaam]"
                       value="1" <?php if ( $options['vraagroepnaam'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagroepnaam]"
                       value="0" <?php if ( $options['vraagroepnaam'] === '0' || $options['vraagroepnaam'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagroepnaam">
                    Moet ook de roepnaam van cursist uitgevraagd worden?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Meisjesnaam uitvragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagmeisjesnaam]"
                       value="1" <?php if ( $options['vraagmeisjesnaam'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagmeisjesnaam]"
                       value="0" <?php if ( $options['vraagmeisjesnaam'] === '0' || $options['vraagmeisjesnaam'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagmeisjesnaam">
                    Moet ook meisjesnaam van cursist uitgevraagd worden?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Interne referentie uitvragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraag_internal_reference]"
                       value="1" <?php if ( $options['vraag_internal_reference'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraag_internal_reference]"
                       value="0" <?php if ( $options['vraag_internal_reference'] === '0' || $options['vraag_internal_reference'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraag_internal_reference">
                    Moet er per cursist een interne referentie worden uitgevraagd?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Kostenplaats uitvragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagcostcentercode]"
                       value="1" <?php if ( $options['vraagcostcentercode'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagcostcentercode]"
                       value="0" <?php if ( $options['vraagcostcentercode'] === '0' || $options['vraagcostcentercode'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagcostcentercode">
                    Wil je dat er de optie 'kostenplaats' wordt gevraagd zodat deze met de booking wordt meegenomen?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Kostenplaats verplicht
                <span style="text-align: left; color: #ff7700;"><b>Nieuw!</b></span>
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagcostcentercodemanatory]"
                       value="1" <?php if ( $options['vraagcostcentercodemanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[vraagcostcentercodemanatory]"
                       value="0" <?php if ( $options['vraagcostcentercodemanatory'] === '0' || $options['vraagcostcentercodemanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-vraagcostcentercodemanatory">
                    Moet dit veld (kostenplaats) ook verplicht zijn? Standaard niet.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Tekst kostenplaats
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[tekstkostenplaats]'
                       value='<?php
				       if ( $options['tekstkostenplaats'] === "" ) {
					       echo "Kostenplaats";
				       } else {
					       echo $options['tekstkostenplaats'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-tekstkostenplaats">
                    Welke tekst moet getoond worden als je kostenplaats uitvraagt (indien bovenstaande ja)?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Personeelsnummer vragen?
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagpersoneelsnummer]"
                       value="1" <?php if ( $options['vraagpersoneelsnummer'] === '1' ) {
					echo 'checked="checked"';
				} ?> /> Ja
                <input type="radio" name="planaday-api-general[vraagpersoneelsnummer]"
                       value="0" <?php if ( $options['vraagpersoneelsnummer'] === '0' || $options['vraagpersoneelsnummer'] === '' ) {
					echo 'checked="checked"';
				} ?> /> Nee
                <p class="description" id="planaday-api-vraagpersoneelsnummer">
                    Wil je dat er de optie 'personeelsnummer' wordt gevraagd zodat deze bij de booking wordt
                    meegenomen
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld geboortedatum cursist
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformdateofbirth]"
                       value="1" <?php if ( $options['toonformdateofbirth'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformdateofbirth]"
                       value="0" <?php if ( $options['toonformdateofbirth'] === '0' || $options['toonformdateofbirth'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformdateofbirth">
                    Moet geboortedatum uitgevraagd worden bij het boeken?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Geboortedatum cursist verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformdateofbirthmanatory]"
                       value="1" <?php if ( $options['toonformdateofbirthmanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformdateofbirthmanatory]"
                       value="0" <?php if ( $options['toonformdateofbirthmanatory'] === '0' || $options['toonformdateofbirthmanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformdateofbirthmanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld geboorteplaats cursist
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformcountryofbirth]"
                       value="1" <?php if ( $options['toonformcountryofbirth'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformcountryofbirth]"
                       value="0" <?php if ( $options['toonformcountryofbirth'] === '0' || $options['toonformcountryofbirth'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformcountryofbirth">
                    Moet geboorteplaats uitgevraagd worden bij het boeken?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Geboorteplaats cursist verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformcountryofbirthmanatory]"
                       value="1" <?php if ( $options['toonformcountryofbirthmanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformcountryofbirthmanatory]"
                       value="0" <?php if ( $options['toonformcountryofbirthmanatory'] === '0' || $options['toonformcountryofbirthmanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformcountryofbirthmanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld email facturatie
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonemailinvoice]"
                       value="1" <?php if ( $options['toonemailinvoice'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonemailinvoice]"
                       value="0" <?php if ( $options['toonemailinvoice'] === '0' || $options['toonemailinvoice'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonemailinvoice">
                    Moet emailadres voor facturatie worden uitgevraagd bij het boeken?<br/>
                    Let op: Kan enkel als men kiest bij boeken voor 'bedrijf = ja'
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld email facturatie verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonemailinvoicemanatory]"
                       value="1" <?php if ( $options['toonemailinvoicemanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonemailinvoicemanatory]"
                       value="0" <?php if ( $options['toonemailinvoicemanatory'] === '0' || $options['toonemailinvoicemanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonemailinvoicemanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld telefoonnummer cursist
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformphonenumber]"
                       value="1" <?php if ( $options['toonformphonenumber'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformphonenumber]"
                       value="0" <?php if ( $options['toonformphonenumber'] === '0' || $options['toonformphonenumber'] === '0' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformphonenumber">
                    Moet telefoonnummer van cursist uitgevraagd worden bij het boeken?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Telefoonnummer cursist verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformphonenumbermanatory]"
                       value="1" <?php if ( $options['toonformphonenumbermanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformphonenumbermanatory]"
                       value="0" <?php if ( $options['toonformphonenumbermanatory'] === '0' || $options['toonformphonenumbermanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformphonenumbermanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld telefoonnummer bedrijf
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformphonenumbercompany]"
                       value="1" <?php if ( $options['toonformphonenumbercompany'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformphonenumbercompany]"
                       value="0" <?php if ( $options['toonformphonenumbercompany'] === '0' || $options['toonformphonenumbercompany'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformphonenumbercompany">
                    Moet telefoonnummer van bedrijf uitgevraagd worden bij het boeken?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Telefoonnummer bedrijf verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformphonenumbercompanymanatory]"
                       value="1" <?php if ( $options['toonformphonenumbercompanymanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformphonenumbercompanymanatory]"
                       value="0" <?php if ( $options['toonformphonenumbercompanymanatory'] === '0' || $options['toonformphonenumbercompanymanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformphonenumbercompanymanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld functie cursist
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformpositionstudent]"
                       value="1" <?php if ( $options['toonformpositionstudent'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformpositionstudent]"
                       value="0" <?php if ( $options['toonformpositionstudent'] === '0' || $options['toonformpositionstudent'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformpositionstudent">
                    Moet functie worden uitgevraagd bij cursist?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Veld functie verplicht
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformpositionstudentmanatory]"
                       value="1" <?php if ( $options['toonformpositionstudentmanatory'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformpositionstudentmanatory]"
                       value="0" <?php if ( $options['toonformpositionstudentmanatory'] === '0' || $options['toonformpositionstudentmanatory'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformpositionstudentmanatory">
                    Moet dit veld ook verplicht zijn?
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Toon optie code95
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonoptiecode95]"
                       value="1" <?php
				if ( ! isset( $options['toonoptiecode95'] ) || $options['toonoptiecode95'] === '' ) {
					$toonoptiecode95 = "0";
				} else {
					$toonoptiecode95 = $options['toonoptiecode95'];
				}

				if ( $toonoptiecode95 == '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonoptiecode95]"
                       value="0" <?php if ( $toonoptiecode95 === '0' || $toonoptiecode95 === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonoptiecode95">
                    Kan cursist aangeven bij inschrijven of zij code95 willen <b>indien cursus code95 bevat</b>?
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Tekst code95 optie
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[tekstcode95optie]'
                       value='<?php
				       if ( ! isset( $options['tekstcode95optie'] ) || $options['tekstcode95optie'] === '' ) {
					       $tekstcode95optie = "Cursus bevat code95, deze deelnemer ook meteen hiervoor aanmelden?";
				       } else {
					       $tekstcode95optie = $options['tekstcode95optie'];
				       }
				       echo $tekstcode95optie;
				       ?>'/>
                <p class="description" id="planaday-api-tekstcode95optie">
                    Welke tekst wil je tonen bij deze code95 optie indien 'ja' en code95 bevat
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Toon optie SOOB
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonoptiesoob]"
                       value="1" <?php
				if ( ! isset( $options['toonoptiesoob'] ) || $options['toonoptiesoob'] === '' ) {
					$toonoptiesoob = "0";
				} else {
					$toonoptiesoob = $options['toonoptiesoob'];
				}

				if ( $toonoptiesoob === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonoptiesoob]"
                       value="0" <?php if ( $toonoptiesoob === '0' || $toonoptiesoob === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonoptiesoob">
                    Kan cursist aangeven bij inschrijven of zij SOOB willen <b>indien cursus SOOB bevat</b>?
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Tekst SOOB optie
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[tekstsooboptie]'
                       value='<?php
				       if ( ! isset( $options['tekstsooboptie'] ) || $options['tekstsooboptie'] === '' ) {
					       $tekstsooboptie = "Cursus bevat SOOB, deze deelnemer ook meteen hiervoor aanmelden?";
				       } else {
					       $tekstsooboptie = $options['tekstsooboptie'];
				       }
				       echo $tekstsooboptie;
				       ?>'/>
                <p class="description" id="planaday-api-tekstsooboptie">
                    Welke tekst wil je tonen bij deze SOOB optie indien 'ja' en SOOB bevat
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Veld opmerkingen
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonstudentremark]"
                       value="1" <?php if ( $options['toonstudentremark'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonstudentremark]"
                       value="0" <?php if ( $options['toonstudentremark'] === '0' || $options['toonstudentremark'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonstudentremark">
                    Moet veld voor opmerkingen getoond worden?
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Titel boekingformulier
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[teksttitelbooking]'
                       value='<?php
				       if ( $options['teksttitelbooking'] === "" ) {
					       echo "Cursus boeken en meteen zelf boeken";
				       } else {
					       echo $options['teksttitelbooking'];
				       }
				       ?>'/>
                <p class="description" id="planaday-api-teksttitelbooking">
                    Welke tekst wil je tonen boven boekingformulier?
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Algemene voorwaarden
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonformalgemenevoorwaarden]"
                       value="1" <?php if ( $options['toonformalgemenevoorwaarden'] === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonformalgemenevoorwaarden]"
                       value="0" <?php if ( $options['toonformalgemenevoorwaarden'] === '0' || $options['toonformalgemenevoorwaarden'] === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonformalgemenevoorwaarden">
                    Moet bezoeker akkoord gaan met de algemene voorwaarden? <br/>
                    Vul in dit geval ook hieronder de url van de algemene voorwaarden in
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="titelrij">
                Url van algemene voorwaarden
            </th>
            <td>
                <input type='text' class='regular-text' name='planaday-api-general[urlalgemenevoorwaarden]'
                       value='<?php echo $options['urlalgemenevoorwaarden']; ?>'/>
                <p class="description" id="planaday-api-urlalgemenevoorwaarden">
                    Vul volledige url in van algemene voorwaarden (inclusief https://)
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                API-attributen
            </th>
            <td>
                <input type="radio" name="planaday-api-general[toonapiattributen]"
                       value="1" <?php
				if ( ! isset( $options['toonapiattributen'] ) || $options['toonapiattributen'] === '' ) {
					$toonapiattributen = "0";
				} else {
					$toonapiattributen = $options['toonapiattributen'];
				}

				if ( $toonapiattributen === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[toonapiattributen]"
                       value="0" <?php if ( $toonapiattributen === '0' || $toonapiattributen === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-toonapiattributen">
                    Wil je de API-attributen zichtbaar & boekbaar hebben?<br>
                    <strong>Let op: </strong>Deze moet je per cursus(sjabloon) ook in je cursus hebben staan<br>
                    <a href="https://planaday.freshdesk.com/support/solutions/articles/11000113919" target="_blank">Zie ook deze handleiding
                        & uitleg</a>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row" class="titelrij">
                Adresgegevens financieel
            </th>
            <td>
                <input type="radio" name="planaday-api-general[vraagfinancieleinfobijbedrijf]"
                       value="1" <?php
				if ( ! isset( $options['vraagfinancieleinfobijbedrijf'] ) || $options['vraagfinancieleinfobijbedrijf'] === '' ) {
					$vraagfinancieleinfobijbedrijf = "0";
				} else {
					$vraagfinancieleinfobijbedrijf = $options['vraagfinancieleinfobijbedrijf'];
				}

				if ( $vraagfinancieleinfobijbedrijf === '1' ) {
					echo 'checked="checked"';
				} ?> />
                Ja
                <input type="radio" name="planaday-api-general[vraagfinancieleinfobijbedrijf]"
                       value="0" <?php if ( $vraagfinancieleinfobijbedrijf === '0' || $vraagfinancieleinfobijbedrijf === '' ) {
					echo 'checked="checked"';
				} ?> />
                Nee
                <p class="description" id="planaday-api-vraagfinancieleinfobijbedrijf">
                    Wil je ook financiele adresgegevens bij bedrijf uitvragen?<br>
                </p>
            </td>
        </tr>

		<?php
	}
}
