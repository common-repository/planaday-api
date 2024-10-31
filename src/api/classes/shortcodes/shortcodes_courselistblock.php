<?php

class shortcodes_courselistblock extends shortcodes
{
    /**
     * @return shortcodes_courselistblock
     */
    public static function planaday_api_get_instance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new static();
        }

        return $instance;
    }

    public function planaday_api_courselistblock($attributes)
    {
        $data = pad_database::pad_give_courselist($attributes);

        ob_start();
        $counter = 0;

        foreach ($data as $dateMarker) {
            foreach ($dateMarker as $course) {
                if ((isset($this->_options['skipcoursewithonlyelearning'])
                     && $this->_options['skipcoursewithonlyelearning'])
                    && $course['daypart_amount'] === '1'
                    && $course['has_elearning'] === '1') {
                    continue;
                }
                if (
                    (isset($this->_options['toonvollecursus'])
                        && $this->_options['toonvollecursus'] === '1')
                    ||
                    (isset($this->_options['toonvollecursus'])
                        && $this->_options['toonvollecursus'] === '0'
                        && $this->planaday_available_places($course['usersavailable'], $course['options']) >= 1)
                ) {
                    if ($course['firstDayPartLocationId'] !== null) {
                        $location = pad_database::pad_db_part('locations', $course['firstDayPartLocationId'], 'city');
                    } else {
                        $location = '-';
                    }

                    $dayparts = pad_database::pad_return_all_dayparts($course['id']);
                    $firstRealDay = pad_database::pad_first_daypart_with_date($course['id'],
                        (isset($this->_options['skipcoursewithonlyelearning']) && $this->_options['skipcoursewithonlyelearning']));
                    $startDate = Planaday_date::give_readable_date($firstRealDay['date']);

                    if (isset($this->_options['toonimginoverzicht'])
                        && $this->_options['toonimginoverzicht'] === '1') {
                        $img = $this->planaday_api_image($course['id']);
                        $image = '<img class="pad-imagecover-overzicht" src="' . $img . '">';
                    } else {
                        $image = null;
                    }

                    if ($course['firstDayPartDate'] !== '') {
                        if ((isset($this->_options['tooncursusdagdelenverleden'])
                                && $this->_options['tooncursusdagdelenverleden'] === '1')
                            || (isset($this->_options['tooncursusdagdelenverleden'])
                                && $this->_options['tooncursusdagdelenverleden'] === '0'
                                && !pad_database::pad_dayparts_in_past($course['id']))
                            || ($course['daypart_amount'] === '1'
                                && $course['has_elearning'] === '1'
                                && pad_database::pad_dayparts_in_past($course['id']))) {
                            ++$counter;

                            $courseLabels = null;
                            $labelArray = explode(',', $course['labels']);
                            $labelAmount = count($labelArray);
                            for ($i = 0; $i < $labelAmount; $i++) {
                                $courseLabels .= "<div class='pad-detail-label'>" . $labelArray[$i];
                                if ($i < $labelAmount - 1) {
                                    $courseLabels .= ", ";
                                }
                                $courseLabels .= '</div>';
                            }

                            echo '<div class="pad-course">';
                            echo '<div class="pad-title"><a href="' . $this->planaday_api_course_link($course['id'], $course['name']) . '">' . $course['name'] . '</a></div>';
                            echo '<div class="pad-date"><i class="fas fa-calendar"></i> ' . __("Startdatum", "planaday-api") . ': ' . $startDate;

                            if (isset($this->_options['startgarantieoverzicht'])
                                && $this->_options['startgarantieoverzicht'] === '1'
                                && $course['start_guaranteed'] === '1') {
                                echo ' <span id="label_startguaranteed" class="label label-success" title="' . __("heeft startgarantie",
                                        "planaday-api") . '"><i class="fas fa-check"></i> ' . __("Startgarantie",
                                        "planaday-api") . '</span> ';
                            }

                            if ($this->planaday_available_places($course['usersavailable'], $course['options']) <= 0) {
                                echo '<div class="pad-full"><i class="fas fa-exclamation-triangle"></i> ' . __("Cursus is vol",
                                        "planaday-api") . ' </div> ';
                            }

                            echo '</div>';

                            if (isset($this->_options['toondetaildagdelenlijstinoverzicht'])
                                && $this->_options['toondetaildagdelenlijstinoverzicht'] === '1') {
                                echo '<ul class="pad-detail-dayparts">';

                                foreach ($dayparts as $daypart) {
                                    $daypartLabels = null;

                                    if (isset($this->_options['toonelearningdagdeelinoverzicht'])
                                        && $this->_options['toonelearningdagdeelinoverzicht'] === '1'
                                        && $daypart['is_elearning'] === 1) {
                                        $elearning = '<div class="pad-courseelearning-detail" title="' . __("cursus bevat elarning", "planaday-api") . '"> 
														<i class="fas fa-graduation-cap"></i> ' . __("e-learning", "planaday-api") . ' </div>';
                                    } else {
                                        $elearning = '<div></div>';
                                    }

                                    if (isset($this->_options['toonlabelindagdeel'])
                                        && $this->_options['toonlabelindagdeel'] === '1'
                                        && $daypart['labels'] !== "") {
                                        $daypartLabels = "<div class='padday-labels' title='dagdeel heeft deze labels'> <i class='fas fa-tags fa-fw'></i>" . $daypart['labels'] . "</div> ";
                                    }

                                    if (isset($this->_options['toonelearningdagdeelinoverzicht'])
                                        && $this->_options['toonelearningdagdeelinoverzicht'] === '1'
                                        && $daypart['is_elearning'] === 1) {
                                        echo sprintf('<li> %s actief vanaf %s %s</li>',
                                            $daypart['name'],
                                            date('d-m-Y', strtotime($daypart['date'])),
                                            $elearning
                                        );
                                    }

                                    if ($daypart['is_elearning'] === '0') {
                                        $locationText = null;
                                        if (isset($this->_options['toonlocatiedagdeeloverzicht'])
                                            && $this->_options['toonlocatiedagdeeloverzicht'] === '1') {
                                            $locationText = ' in ' . pad_database::pad_db_part('locations', $daypart['locationid'], 'city');
                                        }

                                        echo sprintf('<li> %s op %s van %s tot %s%s %s</li>',
                                            $daypart['name'],
                                            date('d-m-Y', strtotime($daypart['date'])),
                                            date('H:i', strtotime($daypart['start_time'])),
                                            date('H:i', strtotime($daypart['end_time'])),
                                            $locationText,
                                            $daypartLabels
                                        );
                                    }
                                }
                                echo '</ul>';
                            }
                            echo '<hr class="line">';

                            if (isset($this->_options['toonimginoverzicht'])
                                && $this->_options['toonimginoverzicht'] === '1') {
                                echo $image;
                            }

                            if (isset($this->_options['toonomschrijvingcursus'])
                                && $this->_options['toonomschrijvingcursus'] === '1') {
                                $textomschrijving = substr($this->planaday_api_var_cleanup($course['description']),
                                    0, $this->_options['limiettekstomschrijving']);
                                echo '<div class="pad-description">' . $textomschrijving . ' </div>';
                            }

                            if (isset($this->_options['toondagdelen'])
                                && $this->_options['toondagdelen'] === '1') {
                                echo '<div class="pad-dayparts" title="' . __("Deze cursus betaat uit",
                                        "planaday-api") . ' ' . $course['daypart_amount'] . ' ' . __("dagdelen",
                                        "planaday-api") . '"> <i class="fas fa-recycle"></i> ' . $course['daypart_amount'] . ' ' . $this->_options['dagdelentekst'] . ' </div>';
                            }

                            if (isset($this->_options['toonlocatiebijoverzicht'])
                                && $this->_options['toonlocatiebijoverzicht'] === '1') {
                                echo '<div class="pad-place" title="' . __("cursus start in deze plaats",
                                        "planaday-api") . '"> <i class="fas fa-map-marker-alt"></i> ' . $location . ' </div>';
                            }

                            if (isset($this->_options['tooncursuselearning'])
                                && $this->_options['tooncursuselearning'] === '1'
                                && $course['has_elearning'] === '1') {
                                echo '<div class="pad-courseelearning" title="' . __("cursus bevat elarning",
                                        "planaday-api") . '"> <i class="fas fa-book"></i> ' . __("e-learning",
                                        "planaday-api") . ' </div>';
                            }

                            if (isset($this->_options['toonbeschikbareplaatsen'])
                                && $this->_options['toonbeschikbareplaatsen'] === '1') {
                                echo '<div class="pad-available" title="' . __("plaatsen beschikbaar",
                                        "planaday-api") . '"> <i class="fas fa-user-cog"></i> ' . $this->planaday_available_places($course['usersavailable'],
                                        $course['options']) . ' ' . __("beschikbaar", "planaday-api") . ' </div>';
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
                                echo '<div class="pad-costs" title="' . __("prijs per persoon",
                                        "planaday-api") . '"> <i class="fas fa-money-bill"></i> &euro; ' . $prijs . ' p.p. ' . $extra . '</div>';
                            }

                            if (isset($this->_options['geldterugoverzicht'])
                                && $this->_options['geldterugoverzicht'] === '1'
                                && $course['moneyback_guaranteed'] === '1') {
                                echo ' <div class="pad-moneygaranteed" title="' . __("niet goed, geld terug garantie!",
                                        "planaday-api") . '"> <i class="fas fa-money-bill-alt"></i> ' . __("Geld terug garantie",
                                        "planaday-api") . ' </div> ';
                            }

                            if (isset($this->_options['tooncode95overzicht'])
                                && $this->_options['tooncode95overzicht'] === '1'
                                && $course['has_code95'] === '1') {
                                echo '<div class="pad-code95" title="' . __("cursus heeft code95",
                                        "planaday-api") . '"> <i class="fas fa-clock fa-fw"></i> ' . __("Code95",
                                        "planaday-api") . ' </div>';
                            }

                            if (isset($this->_options['toonsooboverzicht'])
                                && $this->_options['toonsooboverzicht'] === '1'
                                && $course['has_soob'] === '1') {
                                echo '<div class="pad-soob" title="' . __("cursus heeft SOOB subsidie",
                                        "planaday-api") . '"> <i class="fas fa-truck fa-fw"></i> ' . __("SOOB subsidie",
                                        "planaday-api") . ' </div>';
                            }

                            if (isset($this->_options['tooncursuslabelsoverzicht'])
                                && $this->_options['tooncursuslabelsoverzicht'] === '1'
                                && !empty($course['labels'])) {
                                echo '<div class="pad-labels" title="' . __("cursus heeft deze labels",
                                        "planaday-api") . '"> <i class="fas fa-tags fa-fw"></i> ' . $courseLabels . ' </div>';
                            }

                            if (isset($this->_options['tooncostsremarkoverzicht'])
                                && $this->_options['tooncostsremarkoverzicht'] === '1') {
                                echo '<div id="pad-detail-costs-remarks">' . $course['costsremark'] . '</div>';
                            }

                            if (isset($this->_options['toonbutton'])
                                && $this->_options['toonbutton'] === '1'
                                && $this->planaday_available_places($course['usersavailable'],
                                    $course['options']) >= 1) {
                                echo '<div class="pad-button" title="' . __('Bekijk details van deze cursus',
                                        "planaday-api") . '"><a href="' . $this->planaday_api_course_link($course['id'],
                                        $course['name']) . '"><button type="button" class="btn btn-link">' . __($this->_options['buttontekstoverzicht'],
                                        "planaday-api") . '</button></a></div>';
                            }
                            echo '</div>';
                        }
                    }
                }
            }
        }

        if ($counter === 0) {
            echo __($this->_options['tekstgeencursussen'], "planaday-api");
        }

        return ob_get_clean();
    }
}
