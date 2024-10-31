<?php

require_once('functions.php');

class pad_database
{

    public $_className;
    public $_options;


    public static function pad_table($tableName)
    {
        global $wpdb;

        $table = null;
        switch ($tableName) {
            case 'course':
                $table = $wpdb->prefix . 'padcourse';
                break;
            case 'locations':
                $table = $wpdb->prefix . 'padlocations';
                break;
            case 'dayparts':
                $table = $wpdb->prefix . 'paddayparts';
                break;
        }

        return $table;
    }

    public static function pad_remove_old_data(bool $deleteEverything = false): bool
    {
        global $wpdb;

        if ($deleteEverything === true) {
            $sqlcourse = "DELETE FROM " . self::pad_table('course');
            $wpdb->query($sqlcourse);
            $sqldayparts = "DELETE FROM " . self::pad_table('dayparts');
            $wpdb->query($sqldayparts);
            $sqllocations = "DELETE FROM " . self::pad_table('locations');
            $wpdb->query($sqllocations);

            return true;
        }

        $options = get_option('planaday-api-general');
        $sqlcourse = "DELETE FROM " . self::pad_table('course') . " WHERE lastupdate < '" . Planaday_date::datetime_in_the_past($options['dbcoursehours']) . "'";
        $wpdb->query($sqlcourse);
        $sqldayparts = "DELETE FROM " . self::pad_table('dayparts') . " WHERE lastupdate < '" . Planaday_date::datetime_in_the_past($options['dbdaypartshours']) . "'";
        $wpdb->query($sqldayparts);
        $sqllocations = "DELETE FROM " . self::pad_table('locations') . " WHERE lastupdate < '" . Planaday_date::datetime_in_the_past($options['dbdaypartshours']) . "'";
        $wpdb->query($sqllocations);

        return true;
    }

    /*
     * Deze functie haalt alle cursussen, dagdelen en locaties op
     *
     */
    public static function pad_load_all_data_from_api($attributes, bool $fullReload = false): bool
    {
        global $wpdb;

        $courseListPages = (new shortcodes)->planaday_api_get_course_list($attributes, $fullReload);

		if (count($courseListPages) === 0) {
			return false;
		}

        foreach ($courseListPages as $courseList) {
            if (!array_key_exists('data', $courseList)) {
                continue;
            }

            foreach ($courseList['data'] as $course) {
                // never show closed courses
                if ($course['type'] === 'closed') {
                    continue;
                }

                $courseSql = [
                    "id" => $course['id'],
                    "templateid" => $course['coursetemplate']['id'],
                    "code" => $course['code'],
                    "name" => sanitize_textarea_field($course['name']),
                    "description" => sanitize_textarea_field($course['description']),
                    "type" => $course['type'],
                    "status" => $course['status'],
                    "daypart_amount" => $course['daypart_amount'],
                    "usersmin" => $course['users']['min'],
                    "usersmax" => $course['users']['max'],
                    "usersavailable" => $course['users']['available'],
                    "options" => $course['users']['options'],
                    "costsusers" => $course['costs']['user'],
                    "costsvat" => $course['costs']['vat'],
                    "costsremark" => sanitize_textarea_field($course['costs']['remark']),
                    "start_guaranteed" => $course['start_guaranteed'],
                    "moneyback_guaranteed" => $course['moneyback_guaranteed'],
                    "has_elearning" => $course['has_elearning'],
                    "has_code95" => $course['has_code95'],
                    "level" => null,
                    "image" => null,
                    "language" => $course['language'],
                    "has_soob" => $course['has_soob'],
                    "labels" => strtolower(implode(',', $course['labels'])),
                    "lastupdate" => Planaday_date::current_datetime()
                ];
                $resultcourse = $wpdb->replace(self::pad_table('course'), $courseSql);

                $dayparts = (new shortcodes)->planaday_api_get_dayparts_of_course($course['id']);
                foreach ($dayparts as $daypart) {
                    $daypartSql = [
                        "id" => $daypart['id'],
                        "courseid" => $course['id'],
                        "name" => sanitize_textarea_field($daypart['name']),
                        "start_time" => $daypart['start_time'],
                        "end_time" => $daypart['end_time'],
                        "date" => $daypart['date'],
                        "locationid" => $daypart['locatieid'],
                        "is_elearning" => $daypart['is_elearning'],
                        "description" => sanitize_textarea_field($daypart['description']),
                        "has_code95" => $daypart['has_code95'],
                        "labels" => strtolower(implode(',', $daypart['labels'])),
                        "lastupdate" => Planaday_date::current_datetime(),
                    ];

                    if ($daypart['is_elearning']) {
                        $daypartSql['end_date'] = $daypart['end_date'];
                    }

                    $resultdayparst = $wpdb->replace(self::pad_table('dayparts'), $daypartSql);

                    self::pad_check_if_location_exists_else_insert($daypart['locatieid']);
                }
            }
        }

        return true;
    }

    /*
     * Deze functie haalt 1 cursus met dagdelen en locaties op
     *
     */
    public static function pad_get_one_course($id)
    {
        global $wpdb;
        $options = get_option('planaday-api-general');

        $data = (new shortcodes)->planaday_api_get_one_course($id);

        if (!empty($data)) {
            if ($data['type'] !== 'open') {
                return;
            }

            $datasql = [
                "id" => $data['id'],
                "templateid" => $data['coursetemplate']['id'],
                "code" => $data['code'],
                "name" => sanitize_textarea_field($data['name']),
                "description" => sanitize_textarea_field($data['description']),
                "type" => $data['type'],
                "status" => $data['status'],
                "daypart_amount" => $data['daypart_amount'],
                "usersmin" => $data['users']['min'],
                "usersmax" => $data['users']['max'],
                "usersavailable" => $data['users']['available'],
                "costsusers" => $data['costs']['user'],
                "costsvat" => $data['costs']['vat'],
                "costsremark" => sanitize_textarea_field($data['costs']['remark']),
                "start_guaranteed" => $data['start_guaranteed'],
                "moneyback_guaranteed" => $data['moneyback_guaranteed'],
                "has_elearning" => $data['has_elearning'],
                "has_code95" => $data['has_code95'],
                "level" => $data['level'] ?? null,
                "image" => null,
                "language" => $data['language'],
                "has_soob" => $data['has_soob'],
                "labels" => implode(', ', $data['labels']),
                "lastupdate" => Planaday_date::current_datetime()
            ];
            $wpdb->replace(self::pad_table('course'), $datasql);
            $wpdb->flush();

            $dagdelen = (new shortcodes)->planaday_api_get_dayparts_of_course($data['id']);
            foreach ($dagdelen as $dagdeel) {
                $datasql = [
                    "id" => $dagdeel['id'],
                    "courseid" => $data['id'],
                    "name" => sanitize_textarea_field($dagdeel['name']),
                    "start_time" => $dagdeel['start_time'],
                    "end_time" => $dagdeel['end_time'],
                    "date" => $dagdeel['date'],
                    "locationid" => $dagdeel['locatieid'],
                    "is_elearning" => $dagdeel['is_elearning'],
                    "description" => sanitize_textarea_field($dagdeel['description']),
                    "has_code95" => $dagdeel['has_code95'],
                    "labels" => implode(",", $dagdeel['labels']),
                    "lastupdate" => Planaday_date::current_datetime(),
                ];

                if ($dagdeel['is_elearning']) {
                    $datasql['end_date'] = $dagdeel['end_date'];
                }

                $wpdb->replace(self::pad_table('dayparts'), $datasql);
                $wpdb->flush();

                self::pad_check_if_location_exists_else_insert($dagdeel['locatieid']);
            }
        }

        return;
    }

    public static function pad_check_if_location_exists_else_insert($id)
    {
        global $wpdb;

        if (empty($id)) {
            return;
        }

        if (self::pad_count_rows('locations', $id) === 0) {
            $location = (new shortcodes)->planaday_api_get_location($id);

            if (!empty($location)) {
				$website = null;
				if (array_key_exists( 'website', $location['contact_info'])) {
					$website = $location['contact_info']['website'];
				}

                $datasql = [
                    "id" => $id,
                    "name" => sanitize_textarea_field($location['name']),
                    "street_1" => $location['address']['street_1'],
                    "street_2" => $location['address']['street_2'],
                    "housenumber" => $location['address']['housenumber'],
                    "housenumber_extension" => $location['address']['housenumber_extension'],
                    "zipcode" => $location['address']['zipcode'],
                    "city" => $location['address']['city'],
                    "country" => $location['address']['country'],
                    "lat" => $location['address']['lat'],
                    "lng" => $location['address']['lng'],
                    "phonenumber_1" => str_replace(' ', '', $location['contact_info']['phonenumber_1']),
                    "email" => $location['contact_info']['email'],
                    "website" => $website,
                    "description" => sanitize_textarea_field($location['description']),
                    "capacity" => $location['capacity'],
                    "lastupdate" => Planaday_date::current_datetime(),
                ];

                $resultlocatins = $wpdb->insert(self::pad_table('locations'), (array)$datasql);
            }
        }
    }

    public static function pad_right_db_key($key)
    {
        $return = null;
        switch ($key) {
            case 'end':
            case 'start':
                $return = 'date';
                break;
        }

        return $return;
    }

    public static function pad_give_courselist($attributes, $skipElearning = false)
    {
        global $wpdb;
        $courseIdList = [];

        $options = get_option('planaday-api-general');

        $dbCourseHours = 12;

        if (isset($options['dbcoursehours'])
            && !empty($options['dbcoursehours'])) {
            $dbCourseHours = (int)$options['dbcoursehours'];
        }

        $courseAttributes = $attributes;
        $lastUpdate = self::pad_get_lastupdate('course');

		if (strtotime($lastUpdate) < strtotime('-' . $dbCourseHours . 'hours')) {
            self::pad_load_all_data_from_api($attributes);
        }

        $translate = [
            'now' => " >= " . "'" . Planaday_date::current_date() . "'",
            '+1month' => " <= " . "'" . Planaday_date::plus_months(1) . "'",
            '+1months' => " <= " . "'" . Planaday_date::plus_months(1) . "'",
            '+2months' => " <= " . "'" . Planaday_date::plus_months(2) . "'",
            '+3months' => " <= " . "'" . Planaday_date::plus_months(3) . "'",
            '+4months' => " <= " . "'" . Planaday_date::plus_months(4) . "'",
            '+5months' => " <= " . "'" . Planaday_date::plus_months(5) . "'",
            '+6months' => " <= " . "'" . Planaday_date::plus_months(6) . "'",
            '+7months' => " <= " . "'" . Planaday_date::plus_months(7) . "'",
            '+8months' => " <= " . "'" . Planaday_date::plus_months(8) . "'",
            '+9months' => " <= " . "'" . Planaday_date::plus_months(9) . "'",
            '+10months' => " <= " . "'" . Planaday_date::plus_months(10) . "'",
            '+11months' => " <= " . "'" . Planaday_date::plus_months(11) . "'",
            '+12months' => " <= " . "'" . Planaday_date::plus_months(12) . "'",
        ];

        $counter = 0;
		$unset = ['templateid', 'label', 'showprice', 'withvat' ];
		foreach($unset as $item) {
			if ( isset( $attributes[$item] ) ) {
				unset ( $attributes[$item] );
			}
		}

        $sql = 'SELECT DISTINCT courseid FROM ' . self::pad_table('dayparts') . ' WHERE ';
        foreach ($attributes as $key => $value) {
            ++$counter;
            $sql .= self::pad_right_db_key($key) . $translate[$value];
            if ($counter < count($attributes)) {
                $sql .= ' AND ';
            }
        }
        // Include elearning only
        $sql .= " OR (is_elearning = '1' AND date >= '" . date('Y-m-d', strtotime('-1year')) . "'";
        $sql .= " AND (end_date IS NULL OR end_date >= '" . date('Y-m-d')  . "'))";

        $courseIdsInTimeFrame = $wpdb->get_results($sql, ARRAY_A);
        foreach ($courseIdsInTimeFrame as $courseId) {
            $courseIdList[] = $courseId['courseid'];
        }

        if (count($courseIdList) > 0) {
            $sqlcourse = 'SELECT * FROM ' . self::pad_table('course') . ' WHERE id IN (' . implode(',', $courseIdList) . ')';
            foreach ($courseAttributes as $key => $value) {
                if ($key === 'templateid'
                    && !empty($courseAttributes['templateid'])) {
                    $sqlcourse .= ' AND templateid = ' . $courseAttributes['templateid'];
                }

                if ($key === 'label'
                    && !empty($courseAttributes['label'])) {
                    $sqlcourse .= ' AND labels LIKE "%' . strtolower($courseAttributes['label']) . '%"';
                }
            }

            $courseList = $wpdb->get_results($sqlcourse, ARRAY_A);

            // Add first daypart of course
            $newCourseList = [];
            foreach ($courseList as $key => $course) {
                $firstDaypart = self::pad_first_daypart_with_date($course['id'], false);
                $newKey = strtotime($firstDaypart['date']);
                $newCourseList[$newKey][$key] = $course;
                $newCourseList[$newKey][$key]['firstDayPartDate'] = $firstDaypart['date'];
                $newCourseList[$newKey][$key]['firstDayPartLocationId'] = $firstDaypart['locationid'];
            }

            ksort($newCourseList);
            return $newCourseList;
        }

        return [];
    }

    public static function pad_give_all_courses()
    {
        global $wpdb;
        $options = get_option('planaday-api-general');
        $sqlcourse = 'SELECT * FROM ' . self::pad_table('course');

        return $wpdb->get_results($sqlcourse, ARRAY_A);
    }

    public static function pad_give_all_dayparts()
    {
        global $wpdb;
        $options = get_option('planaday-api-general');
        $sqlcourse = 'SELECT * FROM ' . self::pad_table('dayparts');

        return $wpdb->get_results($sqlcourse, ARRAY_A);
    }

    public static function pad_give_all_locations()
    {
        global $wpdb;
        $options = get_option('planaday-api-general');
        $sqlcourse = 'SELECT * FROM ' . self::pad_table('locations');

        return $wpdb->get_results($sqlcourse, ARRAY_A);
    }

    public static function pad_give_courselist_by_array($courseIdList)
    {
        global $wpdb;

        if ($courseIdList === null) {
            return 0;
        }

        $courseIds = [];
        foreach ($courseIdList as $courseid) {
            $courseIds[] = $courseid['courseid'];
        }

        $courseIds = implode(",", $courseIds);
        $sql = 'SELECT * FROM ' . self::pad_table('course') . ' WHERE id IN (' . $courseIds . ')';
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function pad_give_courselist_by_ids($var)
    {
        global $wpdb;

        if ($var === null) {
            return 0;
        }

        $sql = 'SELECT * FROM ' . self::pad_table('course') . ' WHERE id IN (' . $var . ')';

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function pad_search_course($args)
    {
        global $wpdb;
        $resulttext = null;

        $coursesWithCode95 = [];
        if ((isset($args['code95']))
            && $args['code95'] === 'ja') {
            $sqlcode95 = 'SELECT DISTINCT id FROM ' . self::pad_table('course') . ' WHERE has_code95 = 1';
            $datacode95 = $wpdb->get_results($sqlcode95, ARRAY_A);
            foreach ($datacode95 as $value) {
                $coursesWithCode95 = array_merge($coursesWithCode95, array_values(explode(", ", $value['id'])));
            }
            $resulttext .= "<li>" . __('Er is gezocht in cursusaanbod waarin ook code95 zit', 'planaday-api') . "</li>";
        }

        $coursesWithSoob = [];
        if ((isset($args['soob']))
            && $args['soob'] === 'ja') {
            $sqlsoob = 'SELECT DISTINCT id FROM ' . self::pad_table('course') . ' WHERE has_soob = 1';
            $datasoob = $wpdb->get_results($sqlsoob, ARRAY_A);
            foreach ($datasoob as $value) {
                $coursesWithSoob = array_merge($coursesWithSoob, array_values(explode(", ", $value['id'])));
            }
            $resulttext .= "<li>" . __('Er is gezocht in cursusaanbod waarin ook soob zit', 'planaday-api') . "</li>";
        }

        $coursesWithElearning = [];
        if ((isset($args['elearning']))
            && $args['elearning'] === 'ja') {
            $sqlelearning = 'SELECT DISTINCT id FROM ' . self::pad_table('course') . ' WHERE has_elearning = 1';
            $dataelearning = $wpdb->get_results($sqlelearning, ARRAY_A);
            foreach ($dataelearning as $value) {
                $coursesWithElearning = array_merge($coursesWithElearning, array_values(explode(", ", $value['id'])));
            }
            $resulttext .= "<li>" . __('Er is gezocht in cursusaanbod waarin ook elearning zit', 'planaday-api') . "</li>";
        }

        $coursesWithCorrectLocation = [];
        if (isset($args['location'])
            && !empty($args['location'])) {
            $locationids = implode(", ", $args['location']);
            $sqllocation = 'SELECT DISTINCT courseid FROM ' . self::pad_table('dayparts') . ' WHERE locationid IN (' . $locationids . ')';
            $dataloc = $wpdb->get_results($sqllocation, ARRAY_A);
            foreach ($dataloc as $value) {
                $coursesWithCorrectLocation = array_merge($coursesWithCorrectLocation,
                    array_values(explode(", ", $value['courseid'])));
            }
            $resulttext .= "<li>" . __('Er is gezocht in cursusaanbod waarin aangekozen locatie tenminste in één van de dagdelen zit',
                    'planaday-api') . "</li>";
        }

        $coursesWithCorrectLabel = [];
        if (isset($args['label'])
            && !empty($args['label'])) {
            $labelResult = null;
            foreach ($args['label'] as $value1) {
                $sqllabel = 'SELECT DISTINCT id AS courseid FROM ' . self::pad_table('course') . ' WHERE labels LIKE "%' . esc_sql($value1) . '%"';
                $labelResult[] = $wpdb->get_results($sqllabel, ARRAY_A);
            }
            foreach ($labelResult as $value) {
                $coursesWithCorrectLabel = array_merge($coursesWithCorrectLabel, array_values(explode(", ", $value['courseid'])));
            }
            $labelids = implode(", ", $args['label']);
            $resulttext .= "<li>" . __('Er is gezocht in cursusaanbod met de volgende labels',
                    'planaday-api') . ": " . $labelids . "</li>";
        }

        $coursesWithText = [];
        if (isset($args['q'])
            && $args['q'] !== null) {
            $sql = 'SELECT DISTINCT id AS courseid FROM ' . self::pad_table('course') . ' WHERE name LIKE "%' . esc_sql($_POST['q']) . '%"';
            $textResult = $wpdb->get_results($sql, ARRAY_A);
            foreach ($textResult as $value) {
                $coursesWithText = array_merge($coursesWithText, array_values(explode(", ", $value['courseid'])));
            }
            $resulttext .= "<li>" . __('Er is gezocht in titels van cursussen op zoekterm',
                    'planaday-api') . ": '" . esc_sql($_POST['q']) . "'";
        } else {
            $resulttext .= "<li>" . __('Er was geen tekst opgegeven om specifiek op te zoeken', 'planaday-api') . ". </li>";
        }

        $courseIdsToLookup = array_unique(
            array_merge(
                $coursesWithCorrectLabel,
                $coursesWithCorrectLocation,
                $coursesWithText,
                $coursesWithCode95,
                $coursesWithElearning,
                $coursesWithSoob)
        );
        $courseids = implode(", ", $courseIdsToLookup);

        print "<div class='pad-search-resulttext'><ul>" . $resulttext . "</ul></div>";

        return $courseids;
    }

    public static function pad_db_part($table, $id, $part)
    {
        if (empty($id)
            || empty($table)
            || empty($part)) {
            return null;
        }

        global $wpdb;
        $table = self::pad_table($table);
        $sql = "SELECT " . $part . " FROM $table WHERE id = " . $id . " LIMIT 1";
        $var = $wpdb->get_results($sql, ARRAY_A);

        return isset($var[0]) ? $var[0][$part] : null;
    }

    public static function pad_give_course($courseId)
    {
        if (empty($courseId)) {
            return null;
        }

        global $wpdb;
        $sqlcourse = 'SELECT * FROM ' . self::pad_table('course') . ' WHERE id = ' . $courseId;
        $coursedata = $wpdb->get_results($sqlcourse, ARRAY_A);
        if (empty($coursedata)) {
            $data = self::pad_get_one_course($courseId);
        }

        return $coursedata[0] ?? null;
    }

    public static function pad_first_daypart_with_date($courseId, $skipElearning = false)
    {
        if (empty($courseId)) {
            return null;
        }

        global $wpdb;
        $sql = 'SELECT * FROM ' . self::pad_table('dayparts');
        $sql .= ' WHERE courseid = ' . $courseId;

        if ($skipElearning) {
            $sql .= ' AND is_elearning = 0';
        }

        $sql .= ' AND date IS NOT NULL ORDER BY date, start_time LIMIT 1';
        $data = $wpdb->get_results($sql, ARRAY_A);

        return $data[0] ?? null;
    }

    public static function pad_return_all_dayparts($courseId)
    {
        if (empty($courseId)) {
            return null;
        }

        global $wpdb;
        $sql = 'SELECT * FROM ' . self::pad_table('dayparts') . ' WHERE courseid = ' . $courseId . ' AND date IS NOT NULL ORDER BY date';
        $data = $wpdb->get_results($sql, ARRAY_A);

        return $data ?? null;
    }

    public static function pad_first_daypart_with_date_part($courseId, $part)
    {
        if (empty($courseId)
            || empty($part)) {
            return null;
        }

        global $wpdb;
        $sql = 'SELECT ' . $part . ' FROM ' . self::pad_table('dayparts') . ' WHERE courseid = ' . $courseId . ' AND date IS NOT NULL ORDER BY date limit 1';
        $data = $wpdb->get_results($sql, ARRAY_A);

        return isset($data[0]) ? $data[0][$part] : null;
    }

    public static function pad_count_rows($table, $id)
    {
        global $wpdb;
        $table = self::pad_table($table);
        if ($id === 0) {
            $wpdb->get_results("SELECT * FROM $table");
        } else {
            $wpdb->get_results("SELECT * FROM $table WHERE id = " . $id);
        }

        return $wpdb->num_rows;
    }

    public static function pad_dayparts_in_past($courseId)
    {
        if (empty($courseId)) {
            return null;
        }

        global $wpdb;
        $sql = 'SELECT * FROM ' . self::pad_table('dayparts') . ' WHERE courseid = ' . $courseId . ' AND date < "' . Planaday_date::current_date() . '"';
        $wpdb->get_results($sql);

        if ($wpdb->num_rows >= 1) {
            return true;
        }

        return false;
    }

    public static function pad_get_lastupdate($table = 'course')
    {
        global $wpdb;
        $sql = 'SELECT lastupdate FROM ' . self::pad_table($table) . ' LIMIT 1';
        $data = $wpdb->get_results($sql, ARRAY_A);

        return isset($data[0]) ? $data[0]['lastupdate'] : null;
    }

    public static function pad_get_locations_for_select($selected)
    {
        $select = null;
        global $wpdb;
        $sql = 'SELECT DISTINCT city, id FROM ' . self::pad_table('locations') . ' WHERE city IS NOT NULL ORDER BY city ';
        $data = $wpdb->get_results($sql, ARRAY_A);
        $var = null;

        foreach ($data as $key => $value) {
            foreach ((array)$selected as $val) {
                if ($val === $value['id']) {
                    $select = "checked='true'";
                    break;
                }
                $select = "";
            }
            $var .= "<label class='pad-search-checkbox-label'><input type='checkbox' name='location[]' value='" . $value['id'] . "' " . $select . "> " . $value['city'] . "</label></br>";
        }

        return $var;
    }

    public static function pad_get_labels_for_select($selected1)
    {
        $select1 = [];
        $labels = [];
        $var = null;

        global $wpdb;

        $sql = 'SELECT labels FROM ' . self::pad_table('course') . ' WHERE labels IS NOT NULL ';
        $data = $wpdb->get_results($sql, ARRAY_A);

        foreach ($data as $value) {
            $labels = array_merge($labels, array_values(explode(", ", $value['labels'])));
        }

        foreach (array_unique($labels) as $value) {
            foreach ($selected1 as $val) {
                if (trim($val) === trim($value)) {
                    $select1 = "checked='true'";
                    break;
                }
                $select1 = "";
            }
            $var .= "<label class='pad-search-checkbox-label'><input type='checkbox' name='label[]' value='" . $value . "' " . $select1 . "> " . $value . "</label></br>";
        }

        return $var;
    }

    public static function pad_get_courseids_dayparts_by_location($id)
    {
        global $wpdb;
        $sql = "SELECT DISTINCT courseid FROM " . pad_database::pad_table('dayparts') . " WHERE locationid = " . $id;

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
