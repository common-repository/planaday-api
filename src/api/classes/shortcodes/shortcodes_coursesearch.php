<?php


class shortcodes_coursesearch extends shortcodes {
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

	public function planaday_api_search_form( $args ) {
		ob_start();

		if ( isset( $this->_options['toondebuginfo'] )
		     && $this->_options['toondebuginfo'] === '1' ) {
			echo( sprintf( "<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__, __LINE__,
				print_r( $this->_options, true ) ) );
		}

		if ( $_POST ) {
			$courseIdsFound = pad_database::pad_search_course( $_POST );
			$courses        = pad_database::pad_give_courselist_by_ids( $courseIdsFound );

			$counter = 0;
			if ( $courses !== null ) {
				foreach ( $courses as $course ) {
					if ( ( isset( $this->_options['skipcoursewithonlyelearning'] ) && $this->_options['skipcoursewithonlyelearning'] )
					     && $course['daypart_amount'] === '1'
					     && $course['has_elearning'] === '1' ) {
						continue;
					}
					$firstDaypart = pad_database::pad_first_daypart_with_date( $course['id'],
						(bool) $this->_options['skipcoursewithonlyelearning'] );

					if ( $firstDaypart['date'] !== '' ) {
						if ( ( isset( $this->_options['tooncursusdagdelenverleden'] )
						       && $this->_options['tooncursusdagdelenverleden'] === '1' )
						     || ( isset( $this->_options['tooncursusdagdelenverleden'] )
						          && $this->_options['tooncursusdagdelenverleden'] === '0'
						          && ! pad_database::pad_dayparts_in_past( $course['id'] ) )
						     || ( $course['daypart_amount'] === '1'
						          && $course['has_elearning'] === '1'
						          && pad_database::pad_dayparts_in_past( $course['id'] ) ) ) {
							++ $counter;
						}
					}
				}
			}

			if ( $counter === 0 ) {
				print "Helaas geen cursussen gevonden die voldoen aan zoekterm: '" . sanitize_text_field( $_POST['q'] ) . "'";
				print "<br/>" . __( "Probeer nog het anders nog eens", "planaday-api" ) . ": <br><br>";
				print $this->planaday_api_search_forms( $args );
			} else {
				$this->planaday_api_search_forms( $args );
				print "<div id='pad-search-results'>";

				if ( isset( $this->_options['tooninblockvorm'] )
				     && $this->_options['tooninblockvorm'] === '1' ) {
					foreach ( $courses as $course ) {
						if (
							( isset( $this->_options['toonvollecursus'] )
							  && $this->_options['toonvollecursus'] === '1' )
							||
							( isset( $this->_options['toonvollecursus'] )
							  && $this->_options['toonvollecursus'] === '0'
							  && $this->planaday_available_places( $course['usersavailable'], $course['options'] ) >= 1 )
						) {
							$firstRealDay = pad_database::pad_first_daypart_with_date( $course['id'],
								(bool) $this->_options['skipcoursewithonlyelearning'] );
							$startDate    = Planaday_date::give_readable_date( $firstRealDay['date'] );

							if ( $firstRealDay['locationid'] !== null ) {
								$location = pad_database::pad_db_part( 'locations', $firstRealDay['locationid'], 'city' );
							} else {
								$location = '-';
							}

							if ( $firstRealDay['date'] !== '' ) {
								if ( ( isset( $this->_options['tooncursusdagdelenverleden'] )
								       && $this->_options['tooncursusdagdelenverleden'] === '1' )
								     || ( isset( $this->_options['tooncursusdagdelenverleden'] )
								          && $this->_options['tooncursusdagdelenverleden'] === '0'
								          && ! pad_database::pad_dayparts_in_past( $course['id'] ) )
								     || ( $course['daypart_amount'] === '1'
								          && $course['has_elearning'] === '1'
								          && pad_database::pad_dayparts_in_past( $course['id'] ) ) ) {
									++ $counter;

									echo '<div class="pad-course">';
									echo '<div class="pad-title"><a href="' . $this->planaday_api_course_link( $course['id'],
											$course['name'] ) . '">' . $course['name'] . '</a></div>';
									echo '<div class="pad-date"><i class="fas fa-calendar"></i> ' . __( "Startdatum",
											"planaday-api" ) . ': ' . $startDate;

									if ( isset( $this->_options['startgarantieoverzicht'] )
									     && $this->_options['startgarantieoverzicht'] === '1'
									     && $course['start_guaranteed'] === 1 ) {
										echo ' <span id="label_startguaranteed" class="label label-success" title="' . __( "heeft startgarantie",
												"planaday-api" ) . '"><i class="fas fa-check"></i> ' . __( "Startgarantie",
												"planaday-api" ) . '</span> ';
									}

									if ( $course['usersavailable'] <= 0 ) {
										echo '<div class="pad-full"><i class="fas fa-exclamation-triangle"></i> ' . __( "Cursus is vol",
												"planaday-api" ) . ' </div> ';
									}
									echo '</div>';

									if ( $this->_options['toondetaildagdelenlijstinoverzicht'] ) {
										$dayparts = pad_database::pad_return_all_dayparts( $course['id'] );
										echo '<ul class="pad-detail-dayparts">';
										foreach ( $dayparts as $daypart ) {
											if ( isset( $this->_options['toonelearningdagdeelinoverzicht'] )
											     && $this->_options['toonelearningdagdeelinoverzicht'] === '1'
											     && $daypart['is_elearning'] === 1 ) {
												$elearning = '<div class="pad-courseelearning-detail" title="' . __( "cursus bevat elarning",
														"planaday-api" ) . '"> <i class="fas fa-graduation-cap"></i> ' . __( "e-learning",
														"planaday-api" ) . ' </div>';
											} else {
												$elearning = '<div></div>';
											}

											$daypartLabels = null;
											if ( isset( $this->_options['toonlabelindagdeel'] )
											     && $this->_options['toonlabelindagdeel'] === '1'
											     && $daypart['labels'] !== '' ) {
												$daypartLabels = "<div class=\"padday-labels\" title=\"dagdeel heeft deze labels\"> <i class=\"fas fa-tags fa-fw\"></i>" . $daypart['labels'] . '</div> ';
											}

											if ( isset( $this->_options['toonelearningdagdeelinoverzicht'] )
											     && $this->_options['toonelearningdagdeelinoverzicht'] === '1'
											     && $daypart['is_elearning'] === 1 ) {
												echo sprintf( '<li> %s actief vanaf %s %s</li>',
													$daypart['name'],
													date( 'd-m-Y', strtotime( $daypart['date'] ) ),
													$elearning
												);
											}

											if ( $daypart['is_elearning'] === '0' ) {
												$locationText = null;
												if ( isset( $this->_options['toonlocatiedagdeeloverzicht'] )
												     && $this->_options['toonlocatiedagdeeloverzicht'] === '1' ) {
													$locationText = ' in ' . pad_database::pad_db_part( 'locations', $daypart['locationid'],
															'city' );
												}

												echo sprintf( '<li> %s op %s van %s tot %s%s %s</li>',
													$daypart['name'],
													date( 'd-m-Y', strtotime( $daypart['date'] ) ),
													date( 'H:i', strtotime( $daypart['start_time'] ) ),
													date( 'H:i', strtotime( $daypart['end_time'] ) ),
													$locationText,
													$daypartLabels
												);
											}
										}
										echo '</ul>';
									}
									echo '<hr class="line">';

									if ( isset( $this->_options['toonomschrijvingcursus'] )
									     && $this->_options['toonomschrijvingcursus'] === '1' ) {

										if ( ! isset( $this->_options['limiettekstomschrijving'] ) ) {
											$textLimit = 75;
										} else {
											$textLimit = $this->_options['limiettekstomschrijving'];
										}

										if ( isset( $this->_options['toonomschrijvingcursus'] )
										     && $this->_options['toonomschrijvingcursus'] === '1' ) {
											$textomschrijving = substr( $this->planaday_api_var_cleanup( $course['description'] ),
												0, $this->_options['limiettekstomschrijving'] );
											echo '<div class="pad-description">' . $textomschrijving . ' </div>';
										}

										$courseDescription = $this->planaday_api_var_cleanup( $course['description'] );
										if ( strlen( $course['description'] ) > $this->_options['limiettekstomschrijving'] ) {
											$courseDescription = substr_replace( $courseDescription, "...", 0, $textLimit );
										}
										echo '<div class="pad-description">' . $courseDescription . ' </div>';
									}

									if ( isset( $this->_options['toondagdelen'] )
									     && $this->_options['toondagdelen'] === '1' ) {
										echo '<div class="pad-dayparts" title="' . __( "Deze cursus betaat uit",
												"planaday-api" ) . ' ' . $course['daypart_amount'] . ' ' . __( "dagdelen",
												"planaday-api" ) . '"> <i class="fas fa-recycle"></i> ' . $course['daypart_amount'] . ' ' . $this->_options['dagdelentekst'] . ' </div>';
									}

									if ( isset( $this->_options['toonlocatiebijoverzicht'] )
									     && $this->_options['toonlocatiebijoverzicht'] === '1' ) {
										echo '<div class="pad-place" title="' . __( "cursus start in deze plaats",
												"planaday-api" ) . '"> <i class="fas fa-map-marker-alt"></i> ' . $location . ' </div>';
									}

									if ( isset( $this->_options['tooncursuselearning'] )
									     && $this->_options['tooncursuselearning'] === '1'
									     && $course['has_elearning'] === 1 ) {

										echo '<div class="pad-courseelearning" title="' . __( "cursus bevat elarning",
												"planaday-api" ) . '"> <i class="fas fa-book"></i> ' . __( "e-learning",
												"planaday-api" ) . ' </div>';
									}

									if ( isset( $this->_options['toonbeschikbareplaatsen'] )
									     && $this->_options['toonbeschikbareplaatsen'] === '1' ) {
										echo '<div class="pad-available" title="' . __( "plaatsen beschikbaar",
												"planaday-api" ) . '"> <i class="fas fa-user-cog"></i> ' . $this->planaday_available_places( $course['usersavailable'],
												$course['options'] ) . ' ' . __( "beschikbaar", "planaday-api" ) . ' </div>';
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
										echo '<div class="pad-costs" title="' . __( "prijs per persoon",
												"planaday-api" ) . '"> <i class="fas fa-money-bill"></i> &euro; ' . $prijs . ' p.p. ' . $extra . '</div>';
									}

									if ( isset( $this->_options['geldterugoverzicht'] )
									     && $this->_options['geldterugoverzicht'] === '1'
									     && $course['moneyback_guaranteed'] === 1 ) {
										echo ' <div class="pad-moneygaranteed" title="' . __( "niet goed, geld terug garantie!",
												"planaday-api" ) . '"> <i class="fas fa-money-bill-alt"></i> ' . __( "Geld terug garantie",
												"planaday-api" ) . ' </div> ';
									}

									if ( isset( $this->_options['tooncode95overzicht'] )
									     && $this->_options['tooncode95overzicht'] === '1'
									     && $course['has_code95'] === 1 ) {
										echo '<div class="pad-code95" title="' . __( "cursus heeft code95",
												"planaday-api" ) . '"> <i class="fas fa-clock fa-fw"></i> ' . __( "Code95",
												"planaday-api" ) . ' </div>';
									}

									if ( isset( $this->_options['toonsooboverzicht'] )
									     && $this->_options['toonsooboverzicht'] === '1'
									     && $course['has_soob'] === 1 ) {
										echo '<div class="pad-soob" title="' . __( "cursus heeft SOOB subsidie",
												"planaday-api" ) . '"> <i class="fas fa-truck fa-fw"></i> ' . __( "SOOB subsidie",
												"planaday-api" ) . ' </div>';
									}

									if ( isset( $this->_options['tooncursuslabelsoverzicht'] )
									     && $this->_options['tooncursuslabelsoverzicht'] === '1'
									     && ! empty( $course['labels'] ) ) {

										$courseLabels = null;
										$labelArray   = explode( ',', $course['labels'] );
										$labelAmount  = count( $labelArray );

										for ( $i = 0; $i < $labelAmount; $i ++ ) {
											$courseLabels .= "<div class='pad-detail-label'>" . $labelArray[ $i ];
											if ( $i < $labelAmount - 1 ) {
												$courseLabels .= ', ';
											}
											$courseLabels .= '</div>';
										}

										echo '<div class="pad-labels" title="' . __( "cursus heeft deze labels",
												"planaday-api" ) . '"> <i class="fas fa-tags fa-fw"></i> ' . $courseLabels . ' </div>';
									}

									if ( isset( $this->_options['tooncostsremarkoverzicht'] )
									     && $this->_options['tooncostsremarkoverzicht'] === '1' ) {
										echo '<div id="pad-detail-costs-remarks">' . $course['costsremark'] . '</div>';
									}

									if ( isset( $this->_options['toonbutton'] )
									     && $this->_options['toonbutton'] === '1' ) {
										echo '<div class="pad-button" title="' . __( 'Bekijk details van deze cursus',
												"planaday-api" ) . '"><a href="' . $this->planaday_api_course_link( $course['id'],
												$course['name'] ) . '"><button type="button" class="btn btn-link">' . __( $this->_options['buttontekstoverzicht'],
												"planaday-api" ) . '</button></a></div>';
									}
									echo '</div>';
								}
							}
						}
					}
				} else {
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

					foreach ( $courses as $course ) {
						if (
							( isset( $this->_options['toonvollecursus'] )
							  && $this->_options['toonvollecursus'] === '1' )
							||
							( isset( $this->_options['toonvollecursus'] )
							  && $this->_options['toonvollecursus'] === '0'
							  && $this->planaday_available_places( $course['usersavailable'], $course['options'] ) >= 1 )
						) {
							$firstRealDay = pad_database::pad_first_daypart_with_date( $course['id'],
								(bool) $this->_options['skipcoursewithonlyelearning'] );
							$startDate    = Planaday_date::give_readable_date( $firstRealDay['date'] );

							if ( $firstRealDay['locationid'] !== null ) {
								$location = pad_database::pad_db_part( 'locations', $firstRealDay['locationid'], 'city' );
							} else {
								$location = '-';
							}

							if ( $firstRealDay['date'] !== '' ) {
								if ( ( isset( $this->_options['tooncursusdagdelenverleden'] )
								       && $this->_options['tooncursusdagdelenverleden'] === '1' )
								     || ( isset( $this->_options['tooncursusdagdelenverleden'] )
								          && $this->_options['tooncursusdagdelenverleden'] === '0'
								          && ! pad_database::pad_dayparts_in_past( $course['id'] ) )
								     || ( $course['daypart_amount'] === '1'
								          && $course['has_elearning'] === '1'
								          && pad_database::pad_dayparts_in_past( $course['id'] ) ) ) {
									++ $counter;
									$courseIsFull = null;

									if ( $course['usersavailable'] <= 0 ) {
										$courseIsFull = '<div class="pad-full">( ' . __( "Cursus is vol", "planaday-api" ) . ' )</div> ';
									}
									echo '<tr>';
									echo '<td><a href="' . $this->planaday_api_course_link( $course['id'],
											$course['name'] ) . '">' . $course['name'] . $courseIsFull . '</a></td>';
									echo '<td>' . $startDate . '</td>';

									if ( isset( $this->_options['toondagdelen'] )
									     && $this->_options['toondagdelen'] === '1' ) {
										echo '<td>' . $course['daypart_amount'] . '</td>';
									}

									if ( isset( $this->_options['toonlocatiebijoverzicht'] )
									     && $this->_options['toonlocatiebijoverzicht'] === '1' ) {
										echo '<td>' . $location . '</td>';
									}

									if ( isset( $this->_options['toonprijs'] )
									     && $this->_options['toonprijs'] === '1' ) {
										echo '<td>&euro; ' . number_format( (float) $course['costsusers'], 2, '.', '' ) . ' p.p.</td>';
									}

									if ( isset( $this->_options['toonbutton'] )
									     && $this->_options['toonbutton'] === '1' ) {
										echo '<td class="pad-button" title="' . __( 'Bekijk details van deze cursus',
												'planaday-api' ) . '"><a href="' . $this->planaday_api_course_link( $course['id'],
												$course['name'] ) . '"><button type="button" class="btn btn-link">' . __( $this->_options['buttontekstoverzicht'],
												'planaday-api' ) . '</button></a></td>';
									}
								}
							}
						}
					}
					echo '</table>';
				}

				print "</div>";
			}
		} else {
			$this->planaday_api_search_forms( $args );
		}

		return ob_get_clean();
	}

	public function planaday_api_search_forms( $args ) {
		$select0    = null;
		$select1    = null;
		$_POST['q'] = null;
		$pages      = get_pages( [ 'coursesearch' ] );
		$post_name  = null;
		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, 'coursesearch' ) ) {
				$post_name = $page->post_name;
			}
		}
		echo '<form action="/' . $post_name . '" method="post" novalidate="novalidate" name="padsearching">';
		echo "<div id='pad-search-form'>";

		echo "<div id='pad-search-params'>";
		if ( isset( $this->_options['toontextbijsearch'] )
		     && $this->_options['toontextbijsearch'] === '1' ) {
			echo '<div id="pad-search-title-input" class="pad-search-group">';
			echo "<div id='pad-search-title-label'><label class='pad-search-label' value='" . $_POST['q'] . "' for='q'>" . __( "Zoek op titel" ) . "</label></div>";
			echo "<input class='pad-search-text-input' type='text' id='q' name='q'><br><br>";
			echo '</div>';
		}

		$location = $_POST['location'] ?? null;
		if ( isset( $this->_options['toonlocatiesbijsearch'] )
		     && $this->_options['toonlocatiesbijsearch'] === '1' ) {
			echo "<div id='pad-search-location' class='pad-search-group'>";
			echo "<div id='pad-search-location-label'><label class='pad-search-label' for='location'>" . __( "Locatie's" ) . "</label></div>";
			echo '<div id="pad-search-location-input">';
			echo pad_database::pad_get_locations_for_select( $location );
			echo '</div>';
			echo '</div>';
		}

		$label = $_POST['label'] ?? null;
		if ( isset( $this->_options['toonlabelsbijsearch'] )
		     && $this->_options['toonlabelsbijsearch'] === '1' ) {
			echo "<div id='pad-search-label' class='pad-search-group'>";
			echo "<div id='pad-search-label-label'><label class='pad-search-label' for='label'>" . __( "Labels" ) . "</label></div>";
			echo '<div id="pad-search-label-input">';
			echo pad_database::pad_get_labels_for_select( $label );
			echo '</div>';
			echo '</div>';
		}

		if ( isset( $this->_options['toonsoobbijsearch'] )
		     && $this->_options['toonsoobbijsearch'] === '1' ) {
			if ( isset( $_POST['soob'] ) ) {
				if ( $_POST['soob'] === 'ja' ) {
					$select1 = 'checked="checked"';
				} else {
					$select0 = 'checked="checked"';
				}
			} else {
				$select1 = "";
				$select0 = 'checked="checked"';
			}
			echo "<div id='pad-search-soob' class='pad-search-group'>";
			echo "<div id='pad-search-soob-label'><label class='pad-search-label' for='soob'>" . __( "Soob" ) . "</label></div>";
			echo '<div id="pad-search-soob-input">';
			echo '<label for="soob_ja"><input type="radio" id="soob_ja" name="soob" value="ja" ' . $select1 . '/> Ja</label>';
			echo '<label for="soob_nee"><input type="radio" id="soob_nee" name="soob" value="nee" ' . $select0 . '/> Nee</label>';
			echo '</div>';
			echo '</div>';
		}

		if ( isset( $this->_options['tooncode95bijsearch'] )
		     && $this->_options['tooncode95bijsearch'] === '1' ) {
			if ( isset( $_POST['code95'] ) ) {
				if ( $_POST['code95'] === 'ja' ) {
					$select1 = 'checked="checked"';
				} else {
					$select0 = 'checked="checked"';
				}
			} else {
				$select1 = "";
				$select0 = 'checked="checked"';
			}
			echo "<div id='pad-search-code95' class='pad-search-group'>";
			echo "<div id='pad-search-code95-label'><label class='pad-search-label' for='code95'>" . __( "Code95" ) . "</label></div>";
			echo '<div id="pad-search-code95-input">';
			echo '<label for="code95_ja"><input type="radio" id="code95_ja" name="code95" value="ja" ' . $select1 . '/> Ja</label>';
			echo '<label for="code95_nee"><input type="radio" id="code95_nee" name="code95" value="nee" ' . $select0 . '/> Nee</label>';
			echo '</div>';
			echo '</div>';
		}

		if ( isset( $this->_options['toonelearningbijsearch'] )
		     && $this->_options['toonelearningbijsearch'] === '1' ) {
			if ( isset( $_POST['elearning'] ) ) {
				if ( $_POST['elearning'] === 'ja' ) {
					$select1 = 'checked="checked"';
				} else {
					$select0 = 'checked="checked"';
				}
			} else {
				$select1 = "";
				$select0 = 'checked="checked"';
			}
			echo "<div id='pad-search-elearning' class='pad-search-group'>";
			echo "<div id='pad-search-elearning-label'><label class='pad-search-label' for='elearning'>" . __( "E-learning" ) . "</label></div>";
			echo '<div id="pad-search-elearning-input">';
			echo '<label for="elearning_ja"><input type="radio" id="elearning_ja" name="elearning" value="ja" ' . $select1 . '/> Ja</label>';
			echo '<label for="elearning_nee"><input type="radio" id="elearning_nee" name="elearning" value="nee" ' . $select0 . '/> Nee</label>';
			echo '</div>';
			echo '</div>';
		}

		echo '<div id="pad-search-button"><input id="pad-search-button-button" value=' . __( "Zoek cursus" ) . ' type="submit">';
		echo '<span class="ajax-loader" style="width: 100%"></span></div>';
		echo '</div>';
		echo '</div>';
		echo '</form>';
	}
}
