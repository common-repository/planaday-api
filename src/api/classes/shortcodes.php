<?php

class shortcodes
{
    const COURSESLUG = 'pad-course';

    public $_className;
    public $_options;

    public function __construct()
    {
        $this->_className = 'planaday-api';
        $this->_options = get_option('planaday-api-general');

        if (is_array($this->_options)) {
            foreach ($this->_options as $key => $value) {
                if ($value === null
                    || $value === '') {
                    $this->_options[$key] = '0';
                }
            }
        }
    }

    public function planaday_api_is_active(array $options, string $setting): bool
    {
        return array_key_exists($setting, $options)
            && isset($options[$setting])
            && ($options[$setting] === 1
                || $options[$setting] === '1'
                || $options[$setting] === true
                || strtolower($options[$setting]) === 'ja');
    }

    public function planaday_api_is_inactive(array $options, string $setting): bool
    {
        return array_key_exists($setting, $options)
        && isset($options[$setting])
        && ($options[$setting] === 0
            || $options[$setting] === '0'
            || $options[$setting] === false
            || strtolower($options[$setting]) === 'nee'
            || $options[$setting] === ''
        );
    }

    /**
     *
     */
    public function planaday_api_add_shortcodes()
    {
        add_shortcode('pad-name', [shortcodes_course::planaday_api_get_instance(), 'planaday_api_coursename']); // [pad-name id=x]
        add_shortcode('pad-dates', [shortcodes_course::planaday_api_get_instance(), 'planaday_api_coursedates']); // [pad-dates id=x]
        add_shortcode('pad-dates-locations',
            [shortcodes_course::planaday_api_get_instance(), 'planaday_api_coursedateslocations']); // [pad-dates-locations id=x]
        add_shortcode('pad-price', [shortcodes_course::planaday_api_get_instance(), 'planaday_api_courseprice']); // [pad-price id]
        add_shortcode('pad-price-remark',
            [shortcodes_course::planaday_api_get_instance(), 'planaday_api_coursepriceremark']); // [pad-price-remark id=x]
        add_shortcode('pad-button', [shortcodes_course::planaday_api_get_instance(), 'planaday_api_coursebutton']); // [pad-button id=x]
        add_shortcode('pad-bookingform', [shortcodes_bookingform::planaday_api_get_instance(), 'planaday_api_check_booking']);
        add_shortcode('pad-courseid', [shortcodes_course::planaday_api_get_instance(), 'planaday_give_courseid']); // [pad-courseid]
        add_shortcode('bookingform', [shortcodes_bookingform::planaday_api_get_instance(), 'planaday_api_check_booking']);
        add_shortcode('courselisttable', [
            shortcodes_courselisttable::planaday_api_get_instance(),
            'planaday_api_courselisttable'
        ]); // [courselisttable start=now end=+2months]
        add_shortcode('courselistlist', [
            shortcodes_courselistlist::planaday_api_get_instance(),
            'planaday_api_courselistlist'
        ]); // [courselistlist start=now end=+2months]
        add_shortcode('courselistblock', [
            shortcodes_courselistblock::planaday_api_get_instance(),
            'planaday_api_courselistblock'
        ]); // [courselistblock start=now end=+2months]
        add_shortcode('courselistblock2', [
            shortcodes_courselistblock2::planaday_api_get_instance(),
            'planaday_api_courselistblock2'
        ]); // [courselistblock start=now end=+2months]
        add_shortcode('coursesearch',
            [shortcodes_coursesearch::planaday_api_get_instance(), 'planaday_api_search_form']); // [coursesearch]
        add_shortcode('coursecalendar',
            [shortcodes_coursecalendar::planaday_api_get_instance(), 'planaday_api_course_calendar']); // [coursecalendar]
        add_shortcode(self::COURSESLUG, [shortcodes_course::planaday_api_get_instance(), 'planaday_api_course']); // [course]
    }

    /**
     * @param $id
     * @param string $var
     *
     * @return string
     */
    public function planaday_api_course_link($id, string $var = 'cursus')
    {
        $var = strtolower($var);
        $var = preg_replace("/[^a-zA-Z\d]/", '-', $var);
        $var = preg_replace("/\-+/", '-', $var);
        $var = trim($var, '-');

        return '/' . $this->planaday_api_page_for_shortcode(self::COURSESLUG) . '/' . $id . '/' . $var;
    }


    public static function planaday_available_places($ava, $opt)
    {
        $options = get_option('planaday-api-general');

        if (!isset($options['optiesmeetellen'])) {
            $options['optiesmeetellen'] = '1';
        }

        if ($options['optiesmeetellen'] === '1') {
            $availalble = $ava - $opt;
        } else {
            $availalble = $ava;
        }

        return $availalble;
    }


    /**
     * @param $name
     * @param $value
     * @param $placeholder
     * @param $size
     * @param null $error
     *
     * @return string
     */
    public function planaday_api_inputfield(
        $name,
        $value,
        $placeholder,
        $size,
        $error = null,
        $required = false,
        $type = 'text',
        $id = ''
    )
    {
        $var2 = '';

        if ($error !== null && !empty($_POST)) {
            $var2 = 'formerror';
        }

        if (empty($id)) {
            $givenid = $name;
        } else {
            $givenid = $id;
        }

        $var = "<input name='" . $name .
            "' id='" . $givenid .
            "' value='" . $value .
            "' class='padform " . $name . " " .
            $var2 . "' type='" . $type . "' placeholder='" .
            $placeholder . "' ";

        if ($required) {
            $var .= " required=''";
        }
        $var .= ">";

        return $var;
    }

    /**
     * @param $name
     * @param $value
     * @param $placeholder
     * @param $size
     * @param null $error
     *
     * @return string
     */
    public function planaday_api_textfield(
        $name,
        $value,
        $placeholder,
        $size,
        $error = null,
        $required = false,
        $type = 'text',
        $id = ''
    )
    {
        $var2 = '';

        if ($error !== null) {
            $var2 = 'formerror';
        }

        if (empty($id)) {
            $givenid = $name;
        } else {
            $givenid = $id;
        }

        $var = "<textarea name='" . $name .
            "' id='" . $givenid .
            "' value='" . $value .
            "' class='padform" .
            $var2 . "' type='" . $type . "' placeholder='" .
            $placeholder . "' ";

        if ($required) {
            $var .= " required=''";
        }

        $var .= "></textarea>";

        return $var;
    }

    /**
     * @param $var
     *
     * @return mixed
     */
    public function planaday_api_page_for_shortcode($var)
    {
        $post_name = null;

        $pages = get_pages([$var]);
        foreach ($pages as $page) {
            if (has_shortcode($page->post_content, $var)) {
                $post_name = $page->post_name;
            }
        }

        return $post_name;
    }

    public function planaday_api_dagdeel($id, $element)
    {
        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('daypart/%s', $id),
            []
        );

        return $data[$element] ?? null;
    }

    public function planaday_api_image($id)
    {
        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s/images', $id),
            []
        );


        if (isset($data['data'])) {
            foreach ($data['data'] as $key => $value) {
                if (($value['name'] === 'wordpress')
                    && !empty($value['href'])) {
                    return $value['href'];
                }
            }
        }

        return null;
    }

    public function planaday_api_get_course_list(array $attributes, bool $fullReload = false): array
    {

        $data = [];

        $a = shortcode_atts([
            'start' => 'now',
            'end' => date('Ymd', strtotime($attributes['end'])),
            'url' => null
        ], $attributes);

        if ($fullReload) {
            $a = shortcode_atts([
                'start' => 'now',
                'end' => '+12months',
                'url' => null
            ], $attributes);
        }

        if (!is_array($this->_options)) {
            return $data;
        }

        if (!isset($this->_options['key']) || !isset($this->_options['url'])) {
            return $data;
        }
        $client = client::planaday_api_get_instance();
        $reponse = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            'course/list',
            [
                'start' => date('Ymd', strtotime($a['start'])),
                'end' => date('Ymd', strtotime($a['end'])),
                'url' => $a['url'],
                'offset' => 1
            ]
        );

		if (!is_array( $reponse)) {
			echo $reponse;
			return[];
		}

		$data[] = $reponse;

        $pagetotal = $data['0']['meta']['records'] ?? 1;
        for ($x = 100; $x <= $pagetotal; $x += 100) {
            $data[$x] = $client->call(
                $this->_options['url'],
                $this->_options['key'],
                'course/list',
                [
                    'start' => date('Ymd', strtotime($a['start'])),
                    'end' => date('Ymd', strtotime($a['end'])),
                    'url' => $a['url'],
                    'offset' => $x
                ]
            );
        }

        return $data;
    }

    public function planaday_api_get_one_course($id)
    {
        $client = client::planaday_api_get_instance();

        return $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s', $id),
            []
        );
    }

    public function planaday_api_get_dayparts_of_course($id)
    {
        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s' . '/dayparts', $id),
            []
        );

        $data2 = [];
        if (isset($data['data'])) {
            foreach ($data['data'] as $key => $value) {
                $data2[$key]['id'] = $value['id'];
                $data2[$key]['name'] = $value['name'];
                $data2[$key]['start_time'] = $value['start_time'];
                $data2[$key]['end_time'] = $value['end_time'];
                $data2[$key]['date'] = $value['date'];
                $data2[$key]['locatieid'] = $value['locations'][0]['id'] ?? null;
                $data2[$key]['is_elearning'] = $value['is_elearning'];
                $data2[$key]['has_code95'] = $value['has_code95'];
                $data2[$key]['description'] = $value['description'];
                $data2[$key]['labels'] = $value['labels'];

                if ($value['is_elearning']) {
                    $data2[$key]['end_date'] = $value['date_finish_before'];
                }
            }
        }

        return $data2;
    }

    public function planaday_api_get_amount_dayparts_of_course_without_elearning($id)
    {
        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s' . '/dayparts', $id),
            []
        );
        $teller = 0;
        if (isset($data['data'])) {
            foreach ($data['data'] as $value) {
                if ($value['is_elearning'] == 0) {
                    $teller++;
                }
            }
        }

        return $teller;
    }

    public function planaday_api_get_location($id)
    {
        $url = $this->_options['url'];
        $key = $this->_options['key'];
        $client = client::planaday_api_get_instance();

        return $client->call(
            $url,
            $key,
            sprintf('location/%s', $id),
            []
        );
    }

    public function planaday_api_sanitize($text)
    {
        $text = $this->planaday_api_var_cleanup( $text );
        if ( strlen( $text ) > $this->_options['limiettekstomschrijving'] ) {
            $text = substr_replace(
                html_entity_decode(
                    preg_replace('/(?:<|&lt;)\/?([a-zA-Z]+) *[^<\/]*?(?:>|&gt;)/', '', $text)
                ),
                "...",
				0,
                $this->_options['limiettekstomschrijving']
            );
        }

        return $text . '</br></br>';
    }

    public function planaday_api_var_cleanup($var)
    {
        $var = html_entity_decode($var);
        $var = str_replace('<p>', '', $var);
        return str_replace('</p>', '', $var);
    }

    /*
     * Materialen bij cursus
     * @param id courseid
     * @param onderdeel onderdeel
     */
    public function planaday_api_materialen($id)
    {
        $client = client::planaday_api_get_instance();

        return $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s/materials', $id),
            []
        );
    }

    public function planaday_api_materialen_checklist($id)
    {
        $data = self::planaday_api_materialen($id);
        $var = null;
        foreach ($data['data'] as $k => $v) {
            if (isset($this->_options['btwinofexbtwtonen']) && (string)$this->_options['btwinofexbtwtonen'] === '0') {
                $prijs = number_format((float)$v['selling_price'] + ((float)$v['selling_price'] / 100 * (float)$v['vat']), 2);
				$extra = __(" incl. BTW", "planaday-api");
			} else {
                $prijs = number_format((float)$v['selling_price'], 2);
				$extra = __("ex. BTW", "planaday-api");
            }
			if (isset($this->_options['btwinofexbtwtonenlabel']) && (string)$this->_options['btwinofexbtwtonenlabel'] === '0') {
				$extra = null;
			}
            $var .= "<input type='checkbox' id='" . $v['id'] . "' name='materials[]' value='" . $v['id'] . "'> ";
            $var .= "<label for='" . $v['id'] . "'>" . $v['name'] . " (&euro; " . $prijs . " " . $extra .") ";
            $var .= "</label><br />";
        }

        return $var;
    }

    public function planaday_api_materialen_array($id)
    {
        $data = self::planaday_api_materialen($id);

        return $data['data'];
    }

    public function planaday_api_materialen_bij_cursus($id)
    {
        $data = self::planaday_api_materialen($id);
        $var = 0;

        if (isset($data['data'])) {
            foreach ($data['data'] as $k => $v) {
                $var = +1;
            }

            if ($var >= 1) {
                return true;
            }
        }

        return false;
    }

    public function planaday_api_course_part($courseId, $part = 'title')
    {
        global $wp_query;

        if ($_POST) {
            $courseId = $_POST['course_id'];
        } else {
            $courseId = urldecode($wp_query->query_vars[shortcodes::COURSESLUG]);
        }

        if (isset($wp_query->query_vars[self::COURSESLUG])) {
            $data = pad_database::pad_give_course($courseId);
            $client = client::planaday_api_get_instance();
            $realtimeData = $client->call(
                $this->_options['url'],
                $this->_options['key'],
                sprintf('course/%s', $courseId),
                []
            );

            if (!isset($realtimeData['error'])) {
                $firstRealDay = pad_database::pad_first_daypart_with_date($courseId,
                    (isset($this->_options['skipcoursewithonlyelearning']) && $this->_options['skipcoursewithonlyelearning']));
                $firstDaypart = Planaday_date::give_readable_date($firstRealDay['date']);

                if (isset($this->_options['toondebuginfo'])
                    && $this->_options['toondebuginfo'] === '1') {
                    echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__,
                        __LINE__, print_r($data, true)));
                }

                if ($part === 'title') {
                    echo '<tr> ';
                    echo '<td><i class="fas fa-book fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Cursus",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $realtimeData['name'] . ' </div></td>';
                    echo '</tr> ';
                }

                if ($part === 'labels') {
                    $labels = implode("<br>", $realtimeData['labels']);
                    echo '<tr> ';
                    echo '<td><i class="fas fa-tags fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Labels",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $labels . ' </div></td>';
                    echo '</tr> ';
                }

                if ($part === 'available') {
                    echo '<tr> ';
                    echo '<td><i class="fas fa-user-cog fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Plaatsen vrij",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $realtimeData['users']['available'] . ' </div></td>';
                    echo '</tr> ';
                }

                if ($part === 'costs') {
	                if (isset($this->_options['btwinofexbtwtonen']) && (string)$this->_options['btwinofexbtwtonen'] === '0') {
		                $prijs = number_format((float)$realtimeData['costs']['user'] + ((float)$realtimeData['costs']['user'] / 100 * (float)$realtimeData['costs']['vat']), 2);
	                    $extra = __("incl. BTW", "planaday-api");
					} else {
		                $prijs = number_format((float)$realtimeData['costs']['user'], 2);
						$extra = __("ex. BTW", "planaday-api");
	                }

                    echo '<tr> ';
                    echo '<td><i class="fas fa-money-bill fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Prijs",
                            "planaday-api") . '</div><div id="pad-widget-detail"> &euro;' . $prijs . ' p/p ' . $extra . '</div></td>';
                    echo '</tr> ';
                }

                if ($part === 'daypartsamount') {
                    echo '<tr> ';
                    echo '<td><i class="fas fa-recycle fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Duur",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $realtimeData["daypart_amount"] . ' ' . $this->_options['dagdelentekst'] . '</div></td>';
                    echo '</tr> ';
                }

                if ($part === 'location') {
                    $city = pad_database::pad_db_part('locations', $firstRealDay['locationid'], 'city');
                    echo '<tr> ';
                    echo '<td><i class="fas fa-map-marker-alt fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Locatie",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $city . ' </div></td>';
                    echo '</tr> ';
                }

                if ($part === 'startdate') {
                    echo '<tr> ';
                    echo '<td><i class="fas fa-calendar fa-fx fa-2x"></i></td> <td><div id="pad-widget-title">' . __("Startdatum",
                            "planaday-api") . '</div><div id="pad-widget-detail">' . $firstDaypart . ' </div></td>';
                    echo '</tr> ';
                }
            }
        }
    }

    public function planaday_api_send_mail_to_tutor(?bool $resultOke, ?bool $payed, ?int $planadayPayed, array $postData)
    {
        if (isset($this->_options['mailcursusaanmelding'])
            && $this->_options['mailcursusaanmelding'] !== '1') {

            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $subject = "Aanvraag cursus: '" . $postData['course_name'] . "' (via Wordpress-plugin Planaday)";

            $errorMessage = null;
            if (!$resultOke) {
                $errorMessage = '<span style="color: red;">De aanvraag is NIET goed verlopen en NIET geregistreerd in Planaday!</span><br><br>';
            }

            // Is there a payment involed?
            $multiple = false;
            $payedMessage = null;
            $studentInfo = '<h2 class="pad-h2">Aanvraag informatie:</h2><p class="pad-p">Hier vindt je een overzicht van de aanvraag:</p>';
            if ($payed !== null) {
                $payedMessage = '<h2 class="pad-h2">Betaal informatie';
                if (isset($postData['_mode'])
                    && $postData['_mode'] === 'test') {
                    $payedMessage .= ' <span style="background-color: orange;">(TEST BETALING!)</span>';
                }
                $payedMessage .= '</h2><p class="pad-p">Hier vindt je meer informatie over de betaling van de aanvraag.</p>';

                if ($payed) {
                    $payedMessage .= "- Status betaling: <b style='color: green;'>GESLAAGD</b> ";
                } else {
                    $payedMessage .= "- Status betaling: <b style='color: red;'>NIET GESLAAGD</b> ";
                }
                $payedMessage .= "(details: <a href='" . get_site_url() . "/wp-admin/post.php?post=" . $postData['id'] . "&action=edit'>transactie #" . $postData['id'] . "</a>)<br><br>";

                $payedMessage .= "- Mollie transactie-id: " . $postData['_payment_id'] . "<br>";
                $payedMessage .= "- Bestelling id: " . $postData['id'] . "<br>";
                $payedMessage .= "- Bedrag: " . $postData['_amount'] . "<br>";
                $payedMessage .= "- Betaal methode: " . $postData['_method'] . "<br>";

                if ($planadayPayed === 1) {
                    $payedMessage .= '- De betaling is <b style="color: green;">WEL</b> in Planaday verwerkt bij de aanvraag.';
                } else {
                    $payedMessage .= '- De betaling is <b style="color: red;">NIET</b> in Planaday verwerkt bij de aanvraag.';
                }
                $payedMessage .= '<br><br>';

                // Build student info
                /*
                @Todo Stefan
                    Bij betalen = actief en 1 cursist aangemeld krijgt de L.S. geen cursist erbij in de mail
                */
            }

            if (isset($postData['amount'])
                && $postData['amount'] >= 1) {
                $multiple = true;
                for ($studentId = 0; $studentId <= $postData['amount']; $studentId++) {
                    $studentInfo .= '- <b>Cursist ' . ($studentId + 1) . '</b>: ' . $postData['first_name-' . $studentId] . ' ' . $postData['last_name-' . $studentId] . '. (' . $postData['email-' . $studentInfo] . ') <br>';
                }
            } else {
                $studentInfo .= "- <b>Cursist</b>: " . $postData['first_name-0'] . " " . $postData['last_name-0'] . ". ( " . $postData['email-0'] . " ) <br>";
            }

            if (!empty($postData['company_name'])) {
                $studentInfo .= "- <b>Bedrijf</b>: " . $postData['company_name'] . " (" . $postData['company_email'] . ") <br>";
            }
            $studentInfo .= '<br>';

            $message = "Beste opleider,<br><br>";
            $message .= $errorMessage;
            $message .= "Er is zojuist een aanvraag binnengekomen voor cursus <b>" . $postData['course_name'] . "</b> ";
            $message .= "(" . $postData['course_code'] . ") ";
            $message .= " startend op <b>" . $postData['course_startdate'] . "</b><br>";
            $message .= $studentInfo;
            $message .= $payedMessage;

            $message .= "Indien direct goedkeuren in Planaday uit staat kun je alle details bij 'cursussen -> Aanvragen/annuleringen' bekijken.<br>";
            $message .= "Daar kun je de aanvraag meteen verwerken en (indien gewenst) communicatie sturen naar de cursist.<br><br>";

            if (isset($this->_options['mailcursusaanmeldingcursist'])
                && $this->_options['mailcursusaanmeldingcursist'] === '1'
                && $multiple === false) {
                if (!$postData['email-0']) {
                    $message .= "Let op: Er is GEEN bevestigings mail naar de cursist(en) gestuurd dat de aanvraag is binnengekomen.";
                } else {
                    $message .= "Let op: Er is EEN bevestigings mail naar de cursist gestuurd dat de aanvraag is binnengekomen.";
                }
            }
            $message .= "<br>De aanvraag moet nog wel in Planaday worden bevestigd als automatisch goedkeuren niet actief is!<br><br>";

            $message .= "-- <br>Wordpress Planaday Plugin<br><br>";

            if (isset($options['toondebuginfo'])
                && $options['toondebuginfo'] === '1') {
                $message .= '<h2>Debug</h2>';
                $message .= print_r($postData, true);
                $message .= print_r($this->_options['mailcursusaanmeldingcursist'], true);
                $message .= print_r($multiple, true);
            }

            wp_mail($this->_options['mailcursusaanmelding'], $subject, $message, $headers);
        }
    }

    public function planaday_api_send_mail_to_company(?bool $resultOke, ?bool $payed, ?int $planadayPayed, array $postData)
    {
        if (isset($postData['company_email'])
            && !empty($postData['company_email'])) {

            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $subject = "Aanvraag cursus '" . $postData['course_name'] . "' op " . get_site_url();

            $errorMessage = null;

            // Is there a payment involed?
            $payedMessage = null;
            $studentInfo = '<h2 class="pad-h2">Aanvraag informatie:</h2><p class="pad-p">Hier vindt je een overzicht van de aanvraag:</p>';
            if ($payed !== null) {
                $payedMessage = '<h2 class="pad-h2">Betaal informatie';
                if (isset($postData['_mode'])
                    && $postData['_mode'] === 'test') {
                    $payedMessage .= ' <span style="background-color: orange;">(TEST BETALING!)</span>';
                }
                $payedMessage .= '</h2><p class="pad-p">Hier vindt je meer informatie over de betaling van de aanvraag.</p>';

                if ($payed) {
                    $payedMessage .= "- Status betaling: <b style='color: green;'>GESLAAGD</b><br>";
                } else {
                    $payedMessage .= "- Status betaling: <b style='color: red;'>NIET GESLAAGD</b><br>";
                }

                $payedMessage .= "- Mollie transactie-id: " . $postData['_payment_id'] . "<br>";
                $payedMessage .= "- Bestelling id: " . $postData['id'] . "<br>";
                $payedMessage .= "- Bedrag: " . $postData['_amount'] . "<br>";
                $payedMessage .= "- Betaal methode: " . $postData['_method'] . "<br>";
                $payedMessage .= '<br><br>';

                // Build student info
            }

            if (isset($postData['amount'])
                && $postData['amount'] >= 1) {
                for ($studentId = 0; $studentId <= $postData['amount']; $studentId++) {
                    $studentInfo .= '- </b>Cursist ' . ($studentId + 1) . '</b>: ' . $postData['first_name-' . $studentId] . ' ' . $postData['last_name-' . $studentId] . '. (' . $postData['email-' . $studentInfo] . ') <br>';
                }
            } else {
                $studentInfo .= "- <b>Cursist</b>: " . $postData['first_name-0'] . " " . $postData['last_name-0'] . ". ( " . $postData['email-0'] . " ) <br>";
            }

            if (!empty($postData['company_name'])) {
                $studentInfo .= "- <b>Bedrijf</b>: " . $postData['company_name'] . " (" . $postData['company_email'] . ") <br>";
            }
            $studentInfo .= '<br>';

            if (isset($this->_options['mailbedankttekstbedrijf'])
                && $this->_options['mailbedankttekstbedrijf']) {

                $message = wpautop($this->_options['mailbedankttekstbedrijf']);
                $message = $this->planaday_api_replace_variables($message, $payed, $postData);
                $message = str_replace("{studenteninfo}", $studentInfo, $message);
                $message = str_replace("{betaalinfo}", $payedMessage, $message);
                $message = str_replace("{bedrijfsnaam}", $postData['company_name'], $message);

                if (isset($options['toondebuginfo'])
                    && $options['toondebuginfo'] === '1') {
                    $message .= '<h2>Debug</h2>';
                    $message .= print_r($postData, true);
                }

                wp_mail($postData['company_email'], $subject, $message, $headers);
            }
        }
    }

    public
    function planaday_api_redirect_or_text(
        ?bool  $bookingOke,
        ?bool  $payed,
        ?array $postData
    )
    {
        if ($bookingOke) {
            $redirect = (isset($this->_options['bedanktredirect']) && $this->_options['bedanktredirect'] === '1');
            $permalink = get_permalink($this->_options['bedankurl']);
            $body = wpautop($this->_options['bedankttekst']);
        } else {
            $redirect = (isset($this->_options['bedanktredirectmislukt']) && $this->_options['bedanktredirectmislukt'] === '1');
            $permalink = get_permalink($this->_options['bedankurlmislukt']);
            $body = wpautop($this->_options['bedankttekstmislukt']);
        }

        if ($redirect
            && !empty($permalink)) {
            echo '<script>location.replace("' . $permalink . '");</script>';
        } else {
            $message = "<h3>" . __('Cursus boeking', 'planaday-api') . "</h3>";
            $message .= $body;
            $message = $this->planaday_api_replace_variables($message, $payed, $postData);

            if (isset($options['toondebuginfo'])
                && $options['toondebuginfo'] === '1') {
                $message .= '<h2>Debug (bedankurl)</h2>';
                $message .= print_r($postData, true);
            }
            echo $message;
        }
        exit();
    }

    public
    function planaday_api_send_mail_to_student(
        ?bool $isPayed,
        array $postData
    )
    {
        if (isset($this->_options['mailcursusaanmeldingcursist'], $this->_options['mailbedankttekst'])
            && $this->_options['mailcursusaanmeldingcursist'] === '1'
            && $postData['email-0'] !== ''
            && $this->_options['mailbedankttekst']) {

            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $subject = __("Aanmelding cursus via onze website: ", "planaday-api") . get_bloginfo();
            $message = wpautop($this->_options['mailbedankttekst']);
            $message = $this->planaday_api_replace_variables($message, $isPayed, $postData);

            if (isset($options['toondebuginfo'])
                && $options['toondebuginfo'] === '1') {
                $message .= '<h2>Debug</h2>';
                $message .= print_r($postData, true);
            }

            wp_mail($postData['email-0'], $subject, $message, $headers);
        }
    }


    private
    function planaday_api_replace_variables(
        string $message,
        ?bool  $payed,
        array  $postData
    )
    {
        $fullName = $postData['first_name-0'] . " " . $postData['initials-0'] . " " . $postData['prefix-0'] . " " . $postData['last_name-0'];

        $message = str_replace("{cursus}", $postData['course_name'], $message);
        $message = str_replace("{voornaam}", $postData['first_name-0'], $message);
        $message = str_replace("{achternaam}", $postData['last_name-0'], $message);
        $message = str_replace("{naam}", $fullName, $message);
        $message = str_replace("{website}", get_bloginfo(), $message);
        if ($postData['course_startdate'] === "") {
            $dag=date("w");
            $a_wdagen=array("zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag");
            $dagnummer=date("d");
            $a_maanden=array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
            $maand=$a_maanden[date("m")-1];
            $jaar=date("Y");
            $postData['course_startdate'] = ($a_wdagen[$dag]." ".$dagnummer." ".$maand." ". $jaar);
        }
        $message = str_replace("{startdatum}", $postData['course_startdate'], $message);
        $message = str_replace("{padboekingid}", $postData['booking_id'], $message);

        if (isset($postData['_payment_key'])) {
            $message = str_replace("{idealtransactieid}", $postData['_payment_key'], $message);
            $message = str_replace("{bedrag}", $postData['_amount'], $message);
            $message = str_replace("{betaald}", $payed ? 'Ja' : 'Nee', $message);
        }

        return $message;
    }
}
