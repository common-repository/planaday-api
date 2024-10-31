<?php


class shortcodes_coursecalendar extends shortcodes {
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

	public function planaday_api_course_calendar( $attributes ) {
		$data = pad_database::pad_give_courselist( $attributes );

		$feedarray = [];

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
						(bool) $this->_options['skipcoursewithonlyelearning'] );

					if ( $course['firstDayPartDate'] !== '' ) {
						if ( $course['firstDayPartLocationId'] !== null ) {
							$location = pad_database::pad_db_part( 'locations', $course['firstDayPartLocationId'], 'city' );
						} else {
							$location = '-';
						}

						$start = $firstRealDay['start_time'];
						$end   = $firstRealDay['end_time'];

						$calenderDate = date( 'Y-m-d', strtotime( $firstRealDay['date'] ) );
						$date         = date( 'd-m-Y', strtotime( $firstRealDay['date'] ) );
						$description  = "<b> " . $course['name'] . "</b><br />" . "Begint op " . $date . " (" . $start . " - " . $end . ")<br />Locatie: " . $location;
						$feedarray[]  = [
							"title"       => $course['name'] . " in " . $location,
							"description" => $description,
							"allDay"      => false,
							"start"       => $calenderDate . "T" . $start,
							"end"         => $calenderDate . "T" . $end,
							"editable"    => false,
							"color"       => $this->_options['calendarcolorback'],
							"textColor"   => $this->_options['calendarcolortext'],
							"url"         => $this->planaday_api_course_link( $course['id'], $course['name'] )
						];
					}
				}
			}
		}

		$feed = json_encode( $feedarray, JSON_PRETTY_PRINT );

		ob_start();

		echo "
        <script>
jQuery(document).ready(function () {

    jQuery('#calendar').fullCalendar({
        header: {
            right: 'today,prev,month,agendaWeek,next'
        },
        defaultView: 'month',
        firstDay: 1,
        minTime: '08:00',
        maxTime: '18:00',
        weekNumbers: false,
        buttonText: {
            today: 'vandaag',
            month: 'maand',
            week: 'week'
        },
        monthNames: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        dayNamesShort: ['Zon', 'Maa', 'Din', 'Woe', 'Don', 'Vrij', 'Zat'],
        dayNames: ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'],
        timeFormat: 'HH:mm',
        axisFormat: 'HH:mm',
        weekends: true,
        events: " . $feed . ",
        ";
		if ( isset( $this->_options['tooncalendarmouseover'] ) && $this->_options['tooncalendarmouseover'] === '1' ) {
			echo "
        eventMouseover: function (data, event, view) {
            tooltip = '<div class=\"tooltiptopicevent\">' + data.description + '</div>';

            jQuery(\"body\").append(tooltip);
            jQuery(this).mouseover(function (e) {
                jQuery(this).css('z-index', 10000);
                jQuery('.tooltiptopicevent').fadeIn('500');
                jQuery('.tooltiptopicevent').fadeTo('10', 1.9);
            }).mousemove(function (e) {
                jQuery('.tooltiptopicevent').css('top', e.pageY + 10);
                jQuery('.tooltiptopicevent').css('left', e.pageX + 20);
            });
        },
        eventMouseout: function (data, event, view) {
            jQuery(this).css('z-index', 8);
            jQuery('.tooltiptopicevent').remove();
        }";
		}
		echo "
    });

});
</script>
";

		echo '<div id="calendar" class="planadaycalendar"></div>';

		return ob_get_clean();
	}
}
