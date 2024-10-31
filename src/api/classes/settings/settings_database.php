<?php

class settings_database extends settings
{

    public $_className;
    public $_options;

    public function __construct()
    {
        $this->_className = 'planaday-api';
        $this->_options = get_option('planaday-api-general');


        foreach($this->_options as $key => $value) {
            if ($value === NULL || $value === '') {
                $this->_options[$key] = '0';
            }
        }
    }

	public static function planaday_api_get_instance() {
		static $instance;

		if ( $instance === null ) {
			$instance = new static();
		}

		return $instance;
	}

    /**
     * Show details in current database
     * @todo Eric
     *
     */
    public function planaday_api_show_database()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $database = '<h2>Aantallen</h2>';
        $database .= '<p class="pad-p">Aantal cursussen: ' . pad_database::pad_count_rows('course',0);
        $database .= '<p class="pad-p">Aantal dagdelen: ' . pad_database::pad_count_rows('dayparts',0);
        $database .= '<p>Aantal locaties: ' . pad_database::pad_count_rows('locations',0);

        $database .= '<h2>Cursussen ('.pad_database::pad_count_rows('course',0).')</h2>';
        $database .= '<table border=1 cellpadding="5" cellspacing="2">';
        $database .= '<tr>';
        $database .= '<td>id</td>';
        $database .= '<td>templateid</td>';
        $database .= '<td>code</td>';
        $database .= '<td>name</td>';
        $database .= '<td>description</td>';
        $database .= '<td>type</td>';
        $database .= '<td>status</td>';
        $database .= '<td>daypart_amount</td>';
        $database .= '<td>usersmin</td>';
        $database .= '<td>usersmax</td>';
        $database .= '<td>usersavailable</td>';
        $database .= '<td>costsusers</td>';
        $database .= '<td>costsvat</td>';
        $database .= '<td>costsremark</td>';
        $database .= '<td>start_guaranteed</td>';
        $database .= '<td>moneyback_guaranteed</td>';
        $database .= '<td>has_elearning</td>';
        $database .= '<td>has_code95</td>';
        $database .= '<td>has_soob</td>';
        $database .= '<td>labels</td>';
        $database .= '<td>level</td>';
        $database .= '<td>language</td>';
        $database .= '<td>options</td>';
        $database .= '<td>lastupdate</td>';
        $database .= '</tr>';

        $data = pad_database::pad_give_all_courses();
        foreach ($data as $course) {
            $database .= '<tr>';
            $database .= '<td>'.$course['id'].'</td>';
            $database .= '<td>'.$course['templateid'].'</td>';
            $database .= '<td>'.$course['code'].'</td>';
            $database .= '<td>'.$course['name'].'</td>';
            $database .= '<td>'.$course['description'].'</td>';
            $database .= '<td>'.$course['type'].'</td>';
            $database .= '<td>'.$course['status'].'</td>';
            $database .= '<td>'.$course['daypart_amount'].'</td>';
            $database .= '<td>'.$course['usersmin'].'</td>';
            $database .= '<td>'.$course['usersmax'].'</td>';
            $database .= '<td>'.$course['usersavailable'].'</td>';
            $database .= '<td>'.$course['costsusers'].'</td>';
            $database .= '<td>'.$course['costsvat'].'</td>';
            $database .= '<td>'.$course['costsremark'].'</td>';
            $database .= '<td>'.$course['start_guaranteed'].'</td>';
            $database .= '<td>'.$course['moneyback_guaranteed'].'</td>';
            $database .= '<td>'.$course['has_elearning'].'</td>';
            $database .= '<td>'.$course['has_code95'].'</td>';
            $database .= '<td>'.$course['has_soob'].'</td>';
            $database .= '<td>'.$course['labels'].'</td>';
            $database .= '<td>'.$course['level'].'</td>';
            $database .= '<td>'.$course['language'].'</td>';
            $database .= '<td>'.$course['options'].'</td>';
            $database .= '<td>'.$course['lastupdate'].'</td>';
            $database .= '</tr>';
        }
        $database .= '</table>';

        $database .= '<h2>Dagdelen</h2>';
        $database .= '<table border=1 cellpadding="5" cellspacing="2">';
        $database .= '<tr>';
        $database .= '<td>id</td>';
        $database .= '<td>courseid</td>';
        $database .= '<td>start_time</td>';
        $database .= '<td>end_time</td>';
        $database .= '<td>date</td>';
        $database .= '<td>end_date</td>';
        $database .= '<td>locationid</td>';
        $database .= '<td>is_elearning</td>';
        $database .= '<td>has_code95</td>';
        $database .= '<td>labels</td>';
        $database .= '<td>description</td>';
        $database .= '<td>lastupdate</td>';
        $database .= '</tr>';

        $data2 = pad_database::pad_give_all_dayparts();
        foreach ($data2 as $daypart) {
            $database .= '<tr>';
            $database .= '<td>'.$daypart['id'].'</td>';
            $database .= '<td>'.$daypart['courseid'].'</td>';
            $database .= '<td>'.$daypart['start_time'].'</td>';
            $database .= '<td>'.$daypart['end_time'].'</td>';
            $database .= '<td>'.$daypart['date'].'</td>';
            $database .= '<td>'.$daypart['end_date'].'</td>';
            $database .= '<td>'.$daypart['locationid'].'</td>';
            $database .= '<td>'.$daypart['is_elearning'].'</td>';
            $database .= '<td>'.$daypart['has_code95'].'</td>';
            $database .= '<td>'.$daypart['labels'].'</td>';
            $database .= '<td>'.$daypart['description'].'</td>';
            $database .= '<td>'.$daypart['lastupdate'].'</td>';
            $database .= '</tr>';
        }
        $database .= '</table>';

        $database .= '<h2>Locaties ('.pad_database::pad_count_rows('locations',0).')</h2>';
        $database .= '<table border=1 cellpadding="5" cellspacing="2">';
        $database .= '<tr>';
        $database .= '<td>id</td>';
        $database .= '<td>name</td>';
        $database .= '<td>street_1</td>';
        $database .= '<td>housenumber</td>';
        $database .= '<td>housenumber_extension</td>';
        $database .= '<td>zipcode</td>';
        $database .= '<td>city</td>';
        $database .= '<td>country</td>';
        $database .= '<td>capacity</td>';
        $database .= '<td>description</td>';
        $database .= '<td>lastupdate</td>';
        $database .= '</tr>';

        $data3 = pad_database::pad_give_all_locations();
        foreach ($data3 as $location) {
            $database .= '<tr>';
            $database .= '<td>'.$location['id'].'</td>';
            $database .= '<td>'.$location['name'].'</td>';
            $database .= '<td>'.$location['street_1'].'</td>';
            $database .= '<td>'.$location['housenumber'].'</td>';
            $database .= '<td>'.$location['housenumber_extension'].'</td>';
            $database .= '<td>'.$location['zipcode'].'</td>';
            $database .= '<td>'.$location['city'].'</td>';
            $database .= '<td>'.$location['country'].'</td>';
            $database .= '<td>'.$location['capacity'].'</td>';
            $database .= '<td>'.$location['description'].'</td>';
            $database .= '<td>'.$location['lastupdate'].'</td>';
            $database .= '</tr>';
        }
        $database .= '</table>';

        echo '<div class="wrap">';
        echo '<h2>' . esc_html(get_admin_page_title()) . '</h2>';
        echo $database;
        echo '</div>';
    }
}
