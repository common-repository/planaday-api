<?php

class settings {
	private $_className;

	public function __construct() {
		$this->_className = 'planaday-api';
	}

	public function getClassName() {
		return $this->_className;
	}

	public function ChecksAdminSettingsPage() {
		$options = get_option( 'planaday-api-general' );
		$checks  = [
			'areTablePresent',
			'isPadApiKeyPresent',
			'isPadUrlPresent',
			'isPadThanksPresent',
			'shortcodePadcoursePresent',
			'shortcodeBookingformPresent',
			'isIdealActive',
		];

		foreach ( $checks as $key => $value ) {
			if ( $value === 'isPadApiKeyPresent' ) {
				if ( ! empty( $options['key'] ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'API Key voor Planaday is ingevuld! '
					];
				} else {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'De API Key voor Planaday is niet ingevuld, haal deze op in jouw Planaday omgeving bij "beheer -> publieke apikeys"'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPadUrlPresent' ) {
				if ( ! empty( $options['url'] ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'De URL voor jouw API omgeving van Planaday is ingevuld! '
					];
				} else {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'De URL voor jouw API omgeving van Planaday is niet ingevuld!'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPadThanksPresent' ) {
				if ( ! empty( $options['bedankttekst'] ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Tekst om te bedanken na een boeking is ingevuld!'
					];
				} else {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'De tekst om te tonen na een boeking is niet ingevuld! Wijzig deze bij "instellingen"'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPaytiumGeinstalleerd' ) {
				if ( class_exists( 'Paytium' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Plugin paytium is geinstalleerd. Je kunt gebruik maken van iDeal (activeer wel "betalingen") bij "instellingen"'
					];
				} else {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'Plugin paytium is niet geinstalleerd, als je gebruik wil maken van iDeal bij je boeking, installeer deze dan'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isIdealActive' ) {
				if ( $options['betalingenactief'] === 1 ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Betalingen via iDeal zijn actief. Je kunt gebruikers vooraf laten betalen middels iDeal'
					];
				} else {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'Betalingen via iDeal zijn niet geactiveerd. Als je dit wil, activeer deze dan bij instellingen'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'areTablePresent' ) {
				global $wpdb;
				$query = sprintf( 'SHOW TABLES LIKE \'%s\'', pad_database::pad_table( 'course' ) );
				if ( $wpdb->get_var( $query ) === pad_database::pad_table( 'course' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Tabellen voor opslag zijn aanwezig en aangemaakt.'
					];
					$query = sprintf( 'SHOW COLUMNS FROM %s WHERE field LIKE "end_date"', pad_database::pad_table( 'dayparts' ) );
					if ( count( $wpdb->get_results( $query ) ) > 0 ) {
						$checkout[ $value ] = [
							'check' => '1',
							'text'  => 'De laatste database wijzigingen zijn ingelezen.'
						];
					} else {
						$checkout[ $value ] = [
							'check' => '0',
							'text'  => 'De laatste database wijzigingen zijn nog niet ingelezen! Doe dit via Support -> Database herstel -> Alle tabellen opnieuw opbouwen.'
						];
					}
				} else {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'Tabellen voor opslag zijn niet aanwezig. En dat is niet goed! Raadpleeg je webmaster!'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'shortcodePadcoursePresent' ) {
				if ( shortcode_exists( 'pad-course' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'De shortcode [pad-course] is aanwezig. Je kunt hierdoor de details van je cursussen laten zien'
					];
				} else {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'De shortcode [pad-course] is niet aanwezig. Maak een pagina aan met hierin [pad-course].<br>Anders worden de details van een cursus niet getoond.'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'shortcodeBookingformPresent' ) {
				if ( shortcode_exists( 'bookingform' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'De shortcode [pad-bookingform] is aanwezig. Jouw bezoekers kunnen bij de cursus ook boeken.'
					];
				} else {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'De shortcode [pad-bookingform] is niet aanwezig. Dat geeft niet, maar dan kunnen je bezoekers niet zelf een cursus boeken'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}
		}
	}

	public function ChecksAdminPaymentsPage() {
		$payment              = get_option( 'planaday-api-payment' );
		$paytiumtestkey       = get_option( 'paytium_test_api_key' );
		$paytiumlivekey       = get_option( 'paytium_live_api_key' );
		$enablelivekey        = get_option( 'paytium_enable_live_key' );
		$paymentPageAvailable = post_exists( 'Planaday betalings pagina' );

		$checks = [
			'isPaytiumGeinstalleerd',
			'isPaytiumTestkeyPresent',
			'isPaytiumLivekeyPresent',
			'isTestmodusActive',
			'isPaymentPageAvailable',
		];

		foreach ( $checks as $key => $value ) {
			if ( $value === 'isPaytiumGeinstalleerd' ) {
				if ( class_exists( 'Paytium' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Plugin paytium is geinstalleerd. Je kunt gebruik maken van iDeal'
					];
				} else {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'Plugin paytium is niet geinstalleerd of actief. Als je gebruik wil maken van iDeal bij je boeking, installeer deze dan. Meer informatie
                         over de plugin vindt je hier <a href="https://www.paytium.nl/" target="_blank">Paytium</a>. Klik hier om de plugin makkelijk te installeren
                         <a href="/wp-admin/plugin-install.php?s=paytium&tab=search&type=term">Installeer plugin</a>.'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPaymentPageAvailable'
			     && $paymentPageAvailable ) {
				$checkout[ $value ] = [
					'check' => '1',
					'text'  => 'Betalingspagina is aanwezig. (is een intern pagina, pas deze niet aan!)'
				];
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isTestmodusActive' ) {
				if ( ! empty( $paytiumtestkey )
				     && ( ! empty( $payment['idealtestmodus'] )
				          && $payment['idealtestmodus'] === '1' ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'Testmodus is actief, hierdoor verwerk je enkel testbetalingen'
					];
				} else {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'Testmodus is niet actief, dus of niet gekozen of je bent nog niet live!'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPaytiumLivekeyPresent' ) {
				$checkout = [];
				if ( ! empty( $paytiumlivekey ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'API "live" apikey in Paytium is ingevuld! '
					];
				} elseif ( class_exists( 'Paytium' ) ) {
					$checkout[ $value ] = [
						'check' => 'info',
						'text'  => 'De "live" apikey in Paytium is nog niet ingevuld. Vul deze in bij plugin "<a href="/wp-admin/admin.php?page=paytium">Paytium</a>". Tot deze tijd kun je geen "echte" betalingen ontvangen'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}

			if ( $value === 'isPaytiumTestkeyPresent' ) {
				$checkout = [];
				if ( ! empty( $paytiumtestkey ) ) {
					$checkout[ $value ] = [
						'check' => '1',
						'text'  => 'API "test" apikey in Paytium is ingevuld! Je kunt nu testbetalingen ontvangen'
					];
				} elseif ( class_exists( 'Paytium' ) ) {
					$checkout[ $value ] = [
						'check' => '0',
						'text'  => 'De "test" apikey in Paytium is nog niet ingevuld. Vul deze in bij plugin "<a href="/wp-admin/admin.php?page=paytium">Paytium</a>". Hierna kun je testbetalingen ontvangen'
					];
				}
				print $this->ShowCheckResult( $checkout[ $value ] );
			}
		}
	}

	public function ShowCheckResult( $var ) {
		if ( is_array( $var ) ) {
			$resultText = '<p>';

			if ( $var['check'] === 'info' ) {
				$resultText .= "<img src='/wp-content/plugins/planaday-api/assets/check-info.png' style='width: 16px;'> ";
			} elseif ( $var['check'] === '1' ) {
				$resultText .= "<img src='/wp-content/plugins/planaday-api/assets/check-ok.png' style='width: 16px;'> ";
			} else {
				$resultText .= "<img src='/wp-content/plugins/planaday-api/assets/check-nok.png' style='width: 16px;'> ";
			}
			$resultText .= $var['text'];

			return $resultText . '</p>';
		}

		return null;
	}
}
