<?php

class shortcodes_course extends shortcodes {
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

	public function planaday_api_course() {
		global $wp_query;

		if ( $_POST ) {
			$courseId = idxVal( $_POST['course_id'] );
		} else {
			$queryVars = $wp_query->query_vars;
			if ( array_key_exists( shortcodes::COURSESLUG, $queryVars ) ) {
				$courseId = urldecode( $queryVars[ shortcodes::COURSESLUG ] );
			}
		}

		if ( $courseId !== null ) {
			$client = client::planaday_api_get_instance();
			$data   = $client->call(
				$this->_options['url'],
				$this->_options['key'],
				sprintf( 'course/%s', $courseId ),
				[]
			);

			ob_start();
			if ( isset( $this->_options['toondebuginfo'] )
			     && $this->_options['toondebuginfo'] === '1' ) {
				echo( sprintf( '<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>', __FUNCTION__, __FILE__,
					__LINE__, print_r( $data, true ) ) );
			}

			if ( ! is_array( $data ) ) {
				echo "<div id='pad-errors'>" . __( 'Deze cursus kan niet worden getoond. Neem contact op met de opleider',
						'planaday-api' ) . '.</div>';
			}

			$courseLabels = null;
			$labelArray   = $data['labels'];
			$labelAmount  = count( $labelArray );
			for ( $i = 0; $i < $labelAmount; $i ++ ) {
				$courseLabels .= "<div class='pad-detail-label'>" . $labelArray[ $i ];
				if ( $i < $labelAmount - 1 ) {
					$courseLabels .= ', ';
				}
				$courseLabels .= '</div>';
			}

			if ( isset( $this->_options['toonimgindetail'] )
			     && $this->_options['toonimgindetail'] === '1' ) {
				$img   = $this->planaday_api_image( $data['id'] );
				$image = '<img class="pad-imagecover-detail" src="' . $img . '">';
			} else {
				$image = null;
			}

			if ( $data['daypart_amount'] !== 1 && $data['has_elearning'] !== 1 ) {
				if ( pad_database::pad_dayparts_in_past( $courseId ) ) {
					echo "<div id='pad-info'>" . __( "Let op: Deze cursus bevat",
							"planaday-api" ) . " " . $this->_options['dagdelentekst'] . " " . __( "in het verleden",
							"planaday-api" ) . ".</div>";
				}
			}

			echo '<h2 id="pad-title">' . $data['name'] . '</h2>';
			echo $image;

			if ( $this->planaday_available_places( $data['users']['available'], $data['users']['options'] ) <= 0 ) {
				echo '<div class="pad-detail-full"><i class="fas fa-exclamation-triangle fa-fw"></i> ' . __( "Cursus is vol",
						"planaday-api" ) . ' </div> ';
			}

			if ( isset( $this->_options['toonbeschikbareplaatsen'] )
			     && $this->_options['toonbeschikbareplaatsen'] === '1' ) {
				echo '<div class="pad-detail-available"><i class="fas fa-user-cog fa-fw"></i> ' . __( "Beschikbare plaatsen",
						"planaday-api" ) . ': <span>' . $this->planaday_available_places( $data['users']['available'],
						$data['users']['options'] ) . '</span></div>';
			}

			if ( isset( $this->_options['toonniveau'] )
			     && $this->_options['toonniveau'] === '1' ) {
				echo '<div class="pad-detail-niveau"><i class="fas fa-clipboard-list fa-fw"></i> ' . __( "Niveau",
						"planaday-api" ) . ': <span>' . $data['level'] . '</span></div>';
			}

            if ( isset( $this->_options['toonprijsdetailpagina'] )
                && ($this->_options['toonprijsdetailpagina'] === '1' || $this->_options['toonprijsdetailpagina'] === '') ) {
                if (isset($this->_options['btwinofexbtwtonen']) && (string)$this->_options['btwinofexbtwtonen'] === '0') {
                    $prijs = number_format((float)$data['costs']['user'] + ((float)$data['costs']['user'] / 100 * (float)$data['costs']['vat']), 2);
                     $extra = __("incl. BTW", "planaday-api");
				} else {
                    $prijs = number_format((float)$data['costs']['user'], 2);
					$extra = __("ex. BTW", "planaday-api");
                }
				if (isset($this->_options['btwinofexbtwtonenlabel']) && (string)$this->_options['btwinofexbtwtonenlabel'] === '0') {
					$extra = null;
				}
                echo '<div class="pad-detail-costs"><i class="fas fa-money-bill fa-fw"></i> &euro;' . $prijs .  " " . __("per persoon", "planaday-api") . ' ' . $extra . ' </div>';
            }

			if ( isset( $this->_options['toondetaildagdelen'] )
			     && $this->_options['toondetaildagdelen'] === '1' ) {
				if ( isset( $this->_options['countelearningasdaypart'] )
				     && ( $this->_options['countelearningasdaypart'] === '0' ) ) {
					$daypartscount = shortcodes::planaday_api_get_amount_dayparts_of_course_without_elearning( $courseId );
				} else {
					$daypartscount = $data["daypart_amount"];
				}
				echo '<div class="pad-detail-amount"><i class="fas fa-recycle fa-fw"></i> ' . __( "Bestaat uit",
						"planaday-api" ) . ' ' . $daypartscount . ' ' . $this->_options['dagdelentekst'] . '</div>';
			}

			if ( isset( $this->_options['tooncursuselearning'] )
			     && $this->_options['tooncursuselearning'] === '1'
			     && $data['has_elearning'] === true ) {
				echo '<div class="pad-detail-courseelearning" title="' . __( "cursus bevat elarning",
						"planaday-api" ) . '"> <i class="fas fa-book"></i> ' . __( "e-learning", "planaday-api" ) . ' </div>';
			}

			if ( isset( $this->_options['startgarantiedetail'] )
			     && $this->_options['startgarantiedetail'] === '1'
			     && $data['start_guaranteed'] === true ) {
				echo ' <div class="pad-detail-garanteed"><i class="fas fa-check fa-fw"></i> ' . __( "Start garantie",
						"planaday-api" ) . '</div> ';
			}

			if ( isset( $this->_options['geldterugoverzicht'] )
			     && $this->_options['geldterugoverzicht'] === '1'
			     && $data['moneyback_guaranteed'] === true ) {
				echo ' <div class="pad-detail-moneygaranteed" title="' . __( "niet goed, geld terug garantie!",
						"planaday-api" ) . '"> <i class="fas fa-money-bill-alt"></i> ' . __( "Geld terug garantie",
						"planaday-api" ) . ' </div> ';
			}

			if ( isset( $this->_options['toonsoobdetailcursus'] )
			     && $this->_options['toonsoobdetailcursus'] === '1'
			     && $data['has_soob'] === true ) {
				echo '<div class="pad-detail-soob" title="' . __( "cursus heeft soob subsidie",
						"planaday-api" ) . '"> <i class="fas fa-truck fa-fw"></i> ' . __( "SOOB Subsidie", "planaday-api" ) . ' </div>';
			}

			if ( isset( $this->_options['tooncode95cursus'] )
			     && $this->_options['tooncode95cursus'] === '1'
			     && $data['has_code95'] === true ) {
				echo '<div class="pad-detail-code95" title="' . __( "cursus bevat code95",
						"planaday-api" ) . '"> <i class="fas fa-clock fa-fw"></i> ' . __( "Code95", "planaday-api" ) . ' </div>';
			}

			if ( isset( $this->_options['tooncursuslabelsdetail'] )
			     && $this->_options['tooncursuslabelsdetail'] === '1'
			     && ! empty( $data['labels'] ) ) {
				echo '<div class="pad-detail-labels" title="' . __( "cursus heeft deze labels",
						"planaday-api" ) . '"> <i class="fas fa-tags fa-fw"></i> ' . $courseLabels . ' </div>';
			}

            if ( $this->planaday_api_is_active($this->_options,'toonomschrijvingcursus')) {
                echo '<div class="pad-detail-description">' . $this->planaday_api_var_cleanup( $data['description'] ) . '</div>';
            }

            if ( $this->planaday_api_is_active($this->_options, 'tooncostsremark') ) {
				echo '<div id="pad-detail-costs-remarks">' . $data['costs']['remark'] . '</div>';
			}

			if ($this->planaday_api_is_active($this->_options, 'toondetaildagdelenlijst') ) {
				$dayparts = pad_database::pad_return_all_dayparts( $courseId );

				echo '<p class="pad-detail-dayparts-title">' . $this->_options['dagdelentekst'] . ':</p>';
				echo '<ul class="pad-detail-dayparts">';

				foreach ( $dayparts as $daypart ) {
					if ( isset( $this->_options['toonelearningdagdeel'] )
					     && $this->_options['toonelearningdagdeel'] === '1' ) {
						$elearning = '<div class="pad-courseelearning-detail" title="'. __('cursus bevat elarning', 'planaday-api') .'"> <i class="fas fa-book"></i> ' . __( "e-learning",
								"planaday-api" ) . ' </div>';
					} else {
						$elearning = '<div></div>';
					}

					if ($this->planaday_api_is_active($this->_options, 'toonlocatiedagdeel') ) {
						if ( $daypart['is_elearning'] === '1' ) {
							echo sprintf( '<li> %s actief vanaf %s %s</li>',
								$daypart['name'],
								date( 'd-m-Y', strtotime( $daypart['date'] ) ),
								$elearning
							);
						} else {
							echo sprintf( '<li> %s op %s van %s tot %s in %s</li>',
								$daypart['name'],
								date( 'd-m-Y', strtotime( $daypart['date'] ) ),
								date( 'H:i', strtotime( $daypart['start_time'] ) ),
								date( 'H:i', strtotime( $daypart['end_time'] ) ),
								pad_database::pad_db_part( 'locations', $daypart['locationid'], 'city' )
							);
						}
					} else {
						if ( $daypart['is_elearning'] === '1' ) {
							echo sprintf( '<li> %s actief vanaf %s %s</li>',
								$daypart['name'],
								date( 'd-m-Y', strtotime( $daypart['date'] ) ),
								$elearning
							);
						} else {
							echo sprintf( '<li> %s op %s van %s tot %s</li>',
								$daypart['name'],
								date( 'd-m-Y', strtotime( $daypart['date'] ) ),
								date( 'H:i', strtotime( $daypart['start_time'] ) ),
								date( 'H:i', strtotime( $daypart['end_time'] ) )
							);
						}
					}
					if ( isset( $this->_options['toonomschrijvingdagdeel'] )
					     && $this->_options['toonomschrijvingdagdeel'] === '1'
					     && $daypart['description'] !== null ) {
						echo $daypart['description'];
					}
				}
			}
			echo '</ul>';
			echo '<div style="clear: both"></div>';

			return ob_get_clean();
		}

		ob_start();
		echo __( "Geen cursus geselecteerd.", "planaday-api" );

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_coursename( $attributes ) {
		$a = shortcode_atts( [
			'id' => null,
		], $attributes );

		$data = pad_database::pad_give_course( $a['id'] );

		ob_start();

		if ( $data === null ) {
			echo __( 'Cursus niet gevonden', 'planaday-api' );
			return;
		} else {
			echo '<div id="pad-title">' . $data['name'] . '</div>';
		}

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_coursedates( $attributes ) {
		$a = shortcode_atts( array(
			'id' => null,
		), $attributes );

		ob_start();

		$dayparts = pad_database::pad_return_all_dayparts( $a['id'] );

		echo '<ul class="pad-detail-dayparts">';

		foreach ( $dayparts as $daypart ) {
			echo sprintf( '<li> %s op %s van %s tot %s</li>',
				$daypart['name'],
				date( 'd-m-Y', strtotime( $daypart['date'] ) ),
				date( 'H:i', strtotime( $daypart['start_time'] ) ),
				date( 'H:i', strtotime( $daypart['end_time'] ) )
			);
		}

		echo '</ul>';

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_coursedateslocations( $attributes ) {
		$a = shortcode_atts( [
			'id' => null,
		], $attributes );

		ob_start();

		$dayparts = pad_database::pad_return_all_dayparts( $a['id'] );

		echo '<ul class="pad-detail-dayparts">';

		foreach ( $dayparts as $daypart ) {
			echo sprintf( '<li> %s op %s van %s tot %s in %s</li>',
				$daypart['name'],
				date( 'd-m-Y', strtotime( $daypart['date'] ) ),
				date( 'H:i', strtotime( $daypart['start_time'] ) ),
				date( 'H:i', strtotime( $daypart['end_time'] ) ),
				pad_database::pad_db_part( 'locations', $daypart['locationid'], 'city' )
			);
		}

		echo '</ul>';

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_courseprice( $attributes ) {
		$a = shortcode_atts( [
			'id' => null,
		], $attributes );

		$data = pad_database::pad_give_course( $a['id'] );

		ob_start();

		if ( $data !== null ) {
			if (isset($this->_options['btwinofexbtwtonen']) && (string)$this->_options['btwinofexbtwtonen'] === '0') {
				$prijs = number_format((float)$data['costsusers'] + ((float)$data['costsusers'] / 100 * (float)$data['costsvat']), 2);
			} else {
				$prijs = number_format((float)$data['costsusers'], 2);
			}
			echo '<div class="pad-detail-costs-extra"> &euro; ' . $prijs . ' p/p</div>';
		}

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_coursepriceremark( $attributes ) {
		$a = shortcode_atts( [
			'id' => null,
		], $attributes );

		$data = pad_database::pad_give_course( $a['id'] );

		ob_start();

		if ( $data !== null ) {
			echo '<div class="pad-detail-costs-remarks">' . $data['costsremark'] . '</div>';
		}

		return ob_get_clean();
	}

	/*
	 * Wordt gebruikt door aparte shortcodes
	 */
	public function planaday_api_coursebutton( $attributes ) {
		$a = shortcode_atts( [
			'id' => null,
		], $attributes );

		$data = pad_database::pad_give_course( $a['id'] );

		ob_start();

		if ( $data !== null ) {
			echo '<div class="pad-button-button" title="' . __( 'Bekijk details van deze cursus' ) . '"><a href="' . $this->planaday_api_course_link( $a['id'],
					$data['name'] ) . '"><button type="button" class="btn btn-link">' . __( $this->_options['buttontekstoverzicht'],
					'planaday-api' ) . '</button></a></div>';
		}

		return ob_get_clean();
	}
}
