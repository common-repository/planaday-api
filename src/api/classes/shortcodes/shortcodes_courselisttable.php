<?php

class shortcodes_courselisttable extends shortcodes {
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

	/**
	 * @param $attributes
	 *
	 * @return false|string
	 */
	public function planaday_api_courselisttable( $attributes ) {
		$data = pad_database::pad_give_courselist( $attributes );

		ob_start();
		$counter = 0;

		echo '<table class="pad-courselist">';
		echo '<tr>';
		echo '<thead>';
		echo '<th>' . __( "Cursus", "planaday-api" ) . '</th>';
		echo '<th>' . __( "Begint op", "planaday-api" ) . '</th>';

		if ( isset( $this->_options['toondagdelen'] )
		     && $this->_options['toondagdelen'] === '1' ) {
			echo '<th>' . __( "Aantal dagdelen", "planaday-api" ) . '</th>';
		}
		if ( isset( $this->_options['toonlocatiebijoverzicht'] )
		     && $this->_options['toonlocatiebijoverzicht'] === '1' ) {
			echo '<th>' . __( "Locatie", "planaday-api" ) . '</th>';
		}
		if ( isset( $this->_options['toonprijs'] )
		     && $this->_options['toonprijs'] === '1' ) {
			echo '<th>' . __( "Prijs", "planaday-api" ) . '</th>';
		}
		if ( isset( $this->_options['toonbutton'] )
		     && $this->_options['toonbutton'] === '1' ) {
			echo '<th>' . __( "Bekijk", "planaday-api" ) . '</th>';
		}
		echo '</thead>';
		echo '</tr>';

		foreach ( $data as $dateMarker ) {
			foreach ( $dateMarker as $course ) {
				if ( ( isset( $this->_options['skipcoursewithonlyelearning'] ) && $this->_options['skipcoursewithonlyelearning'] ) && $course['daypart_amount'] === '1' && $course['has_elearning'] === '1' ) {
					continue;
				}
				if (
					( isset( $this->_options['toonvollecursus'] ) && $this->_options['toonvollecursus'] === '1' ) ||
					( isset( $this->_options['toonvollecursus'] ) && $this->_options['toonvollecursus'] === '0'
					  && $this->planaday_available_places( $course['usersavailable'], $course['options'] ) >= 1 )
				) {
					if ( $course['firstDayPartLocationId'] !== null ) {
						$location = pad_database::pad_db_part( 'locations', $course['firstDayPartLocationId'], 'city' );
					} else {
						$location = '-';
					}

					if ( $course['firstDayPartDate'] !== '' ) {
						if ( ( isset( $this->_options['tooncursusdagdelenverleden'] )
						       && $this->_options['tooncursusdagdelenverleden'] === '1' )
						     || ( isset( $this->_options['tooncursusdagdelenverleden'] )
						          && $this->_options['tooncursusdagdelenverleden'] === '0'
						          && ! pad_database::pad_dayparts_in_past( $course['id'] ) )
						     || ( $course['daypart_amount'] === '1'
						          && $course['has_elearning'] === '1'
						          && pad_database::pad_dayparts_in_past( $course['id'] ) ) ) {
							$counter ++;
							$fullText = '';

							if ( $course['usersavailable'] <= 0 ) {
								$fullText = '<div class="pad-full">( ' . __( "Cursus is vol", "planaday-api" ) . ' )</div> ';
							}

							echo '<tr>';
							echo '<td><a href="' . $this->planaday_api_course_link( $course['id'],
									$course['name'] ) . '">' . $course['name'] . $fullText . '</a></td>';

							echo '<td>' . Planaday_date::give_readable_date( $course['firstDayPartDate'] ) . '</td>';

							if ( isset( $this->_options['toondagdelen'] )
							     && $this->_options['toondagdelen'] === '1' ) {
								echo '<td>' . $course['daypart_amount'] . '</td>';
							}

							if ( isset( $this->_options['toonlocatiebijoverzicht'] )
							     && $this->_options['toonlocatiebijoverzicht'] === '1' ) {
								echo '<td>' . $location . '</td>';
							}

							$showPrice = isset( $this->_options['toonprijs'] ) && (string) $this->_options['toonprijs'] === '1';
							if ( isset( $attributes['showprice'] ) ) {
								$showPrice = $attributes['showprice'] === '1';
							}
							$includingVat = isset( $this->_options['btwinofexbtwtonen'] ) && (string) $this->_options['btwinofexbtwtonen'] === '0';
							if ( isset( $attributes['withvat'] ) ) {
								$includingVat = $attributes['withvat'] === '1';
							}

							if ( $showPrice ) {
								if ( $includingVat ) {
									$prijs = number_format( (float) $course['costsusers'] + ( (float) $course['costsusers'] / 100 * (float) $course['costsvat'] ),
										2 );
									$extra = __( "incl. BTW", "planaday-api" );
								} else {
									$prijs = number_format( (float) $course['costsusers'], 2 );
									$extra = __( "ex. BTW", "planaday-api" );
								}
								if ( isset( $this->_options['btwinofexbtwtonenlabel'] ) && (string) $this->_options['btwinofexbtwtonenlabel'] === '0' ) {
									$extra = null;
								}
								echo '<td>&euro; ' . $prijs . ' p.p. ' . $extra . '</td>';
							}

							if ( isset( $this->_options['toonbutton'] )
							     && $this->_options['toonbutton'] === '1'
							     && $this->planaday_available_places( $course['usersavailable'], $course['options'] ) ) {
								echo '<td class="pad-button" title="' . __( 'Bekijk details van deze cursus',
										'planaday-api' ) . '"><a href="' . $this->planaday_api_course_link( $course['id'],
										$course['name'] ) . '"><button type="button" class="btn btn-link">' . __( $this->_options['buttontekstoverzicht'],
										'planaday-api' ) . '</button></a></td>';
							}
						}
					}
				}
			}
		}
		echo '</table>';

		if ( $counter === 0 ) {
			echo __( $this->_options['tekstgeencursussen'], "planaday-api" );
		}

		return ob_get_clean();
	}
}
