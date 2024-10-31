<?php


class shortcodes_courselistlist extends shortcodes {
	/**
	 * @return shortcodes
	 */
	public static function planaday_api_get_instance() {
		static $instance;

		if ( $instance === null ) {
			$instance = new static();
		}

		return $instance;
	}

	public function planaday_api_courselistlist( $attributes ) {
		$data = pad_database::pad_give_courselist( $attributes );

		ob_start();

		$counter = 0;

		echo '<ul>';
		foreach ( $data as $dateMarker ) {
			foreach ( $dateMarker as $course ) {
                if ( ( isset( $this->_options['skipcoursewithonlyelearning'] ) && $this->_options['skipcoursewithonlyelearning'] ) && $course['daypart_amount'] === '1' && $course['has_elearning'] === '1' ) {
                    continue;
                }
				if (
					( isset( $this->_options['toonvollecursus'] )
					  && $this->_options['toonvollecursus'] === '1' )
					||
					( isset( $this->_options['toonvollecursus'] )
					  && $this->_options['toonvollecursus'] === '0'
					  && $this->planaday_available_places( $course['usersavailable'], $course['options'] ) >= 1 )
				) {
					$firstRealDay = pad_database::pad_first_daypart_with_date( $course['id'],
						( isset( $this->_options['skipcoursewithonlyelearning'] ) && $this->_options['skipcoursewithonlyelearning'] ) );
					$startDate    = Planaday_date::give_readable_date( $firstRealDay['date'] );

					if ( $course['firstDayPartDate'] !== '' ) {
						if ( ( isset( $this->_options['tooncursusdagdelenverleden'] )
						       && $this->_options['tooncursusdagdelenverleden'] === '1' )
						     || ( isset( $this->_options['tooncursusdagdelenverleden'] )
						          && $this->_options['tooncursusdagdelenverleden'] === '0'
						          && ! pad_database::pad_dayparts_in_past( $course['id'] ) )
						     || ( $course['daypart_amount'] === '1'
						          && $course['has_elearning'] === '1'
						          && pad_database::pad_dayparts_in_past( $course['id'] ) ) ) {
							++ $counter;

							echo '<li>';
							echo $course['name'] . ' ' . __('start op', 'planaday-api') . ' ' . $startDate . '.';

							if ( isset( $this->_options['startgarantieoverzicht'] )
							     && $this->_options['startgarantieoverzicht'] === '1'
							     && $course['start_guaranteed'] === 1 ) {
								echo ' <span id="startguaranteed" title="' . __( "heeft startgarantie", "planaday-api" ) . '">
 									   <i class="fas fa-check"></i> ' . __( "Startgarantie","planaday-api" ) . '</span> ';
							}

							if ( $this->planaday_available_places( $course['usersavailable'], $course['options'] ) <= 0 ) {
								echo '<div class="pad-full"> ' . __( "Cursus is vol", "planaday-api" ) . ' </div> ';
							}

							if ( isset( $this->_options['toondagdelen'] )
							     && $this->_options['toondagdelen'] === '1' ) {
								echo __(' Met', 'planaday-api'). ' ' . $course['daypart_amount'] . ' ' . $this->_options['dagdelentekst'] . '. ';
							}

							$showPrice = isset($this->_options['toonprijs']) && (string)$this->_options['toonprijs'] === '1';
							if (isset($attributes['showprice'])) {
								$showPrice = $attributes['showprice'] === '1';
							}
							$includingVat = isset($this->_options['btwinofexbtwtonen']) && (string)$this->_options['btwinofexbtwtonen'] === '0';
							if (isset($attributes['withvat'])) {
								$includingVat = $attributes['withvat'] === '1';
							}

							if ($showPrice) {
                                if ($includingVat) {
                                    $prijs = number_format((float)$course['costsusers'] + ((float)$course['costsusers'] / 100 * (float)$course['costsvat']), 2);
                                    $extra = __("incl. BTW", "planaday-api");
								} else {
                                    $prijs = number_format((float)$course['costsusers'], 2);
									$extra = __("ex. BTW", "planaday-api");
                                }
								if (isset($this->_options['btwinofexbtwtonenlabel']) && (string)$this->_options['btwinofexbtwtonenlabel'] === '0') {
									$extra = null;
								}
								echo __(' Voor', 'planaday-api') . ' &euro; ' . $prijs . __('p.p.', 'planaday-api') . ' ' . $extra;
							}

							if ( isset( $this->_options['geldterugoverzicht'] )
							     && $this->_options['geldterugoverzicht'] === '1'
							     && $course['moneyback_guaranteed'] === 1 ) {
								echo ' <div class="pad-moneygaranteed" title="' . __( "niet goed, geld terug garantie!",
										"planaday-api" ) . '"> ' . __( "Geld terug garantie", "planaday-api" ) . ' </div> ';
							}

							if ( $this->planaday_available_places( $course['usersavailable'], $course['options'] ) >= 1 ) {
								echo ' <a class="pad-list-detail-button" href="' . $this->planaday_api_course_link( $course['id'],
										$course['name'] ) . '">' . __( "Details",
										"planaday-api" ) . '</a>';
							}

							echo '</li>';
						}
					}
				}
			}
		}
		echo '</ul>';

		if ( $counter === 0 ) {
			echo __( $this->_options['tekstgeencursussen'], "planaday-api" );
		}

		return ob_get_clean();
	}
}
