<?php

class shortcodes_bookingform extends shortcodes
{
    /**
     * @return shortcodes
     */
    public static function planaday_api_get_instance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @return false|string
     */
    public function planaday_api_check_booking()
    {
        ob_start();
        global $wp_query;

        if (isset($this->_options['toondebuginfo']) && $this->_options['toondebuginfo'] === '1') {
            echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__, __LINE__,
                print_r($this->_options, true)));
        }
        $msg = null;
        $naam = null;
        $email = null;
        $adres = null;
        $courseid = null;
        $data = null;

        if ($_POST) {
            $courseid = $_POST['course_id'];
        } else {
            $queryVars = $wp_query->query_vars;
            if (array_key_exists(shortcodes::COURSESLUG, $queryVars)) {
                $courseid = urldecode($queryVars[shortcodes::COURSESLUG]);
            }
        }
        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%d', (int)$courseid),
            []
        );

        if (!isset($data['users'])) {
            ob_start();
            echo '<br/><br/>' . __("Geen valide resultaat uit Planaday. Een cursus boeken niet mogelijk!", "planaday-api");

            return ob_get_clean();
        }

        if (self::planaday_available_places($data['users']['available'], $data['users']['options']) <= 0) {
            ob_start();
            echo '<br/><br/>' . __("Cursus is vol, daarom is aanmelden niet meer mogelijk!", "planaday-api");

            return ob_get_clean();
        }

        if ($_POST) {
            $companychoice = idxVal($_POST['companychoice']);
        } else {
            $companychoice = $this->_options['voorkeurbooleancompany'];
        }

        if ($_POST) {
            $errors = [];

            if (isset($this->_options['toondebuginfo']) && $this->_options['toondebuginfo'] === '1') {
                echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__,
                    __LINE__, print_r($_POST, true)));
            }
            if (isset($this->_options['toonformalgemenevoorwaarden']) && $this->_options['toonformalgemenevoorwaarden'] === '1'
                && sanitize_text_field($_POST['algemenevoorwaarden']) !== 'ja') {
                $errors['algemenevoorwaarden'] = __("Om te boeken moet je akkoord gaan met de algemene voowaarden", "planaday-api");
            }

            if (sanitize_text_field($_POST['course_id']) === '') {
                $errors['course_id'] = __("Geen cursusid opgegeven", "planaday-api");
            }

            /* 0 = company / 1 = particulier */
            if ($_POST['companychoice'] === 'ja') {
                if (empty(sanitize_text_field($_POST['company_name']))) {
                    $errors['company_name'] = __("Geen bedrijfsnaam opgegeven", "planaday-api");
                }

                if (empty(sanitize_text_field($_POST['company_address']))) {
                    $errors['company_address'] = __("Geen adres bij bedrijf opgegeven", "planaday-api");
                }

                if (empty(sanitize_text_field($_POST['company_house_number']))) {
                    $errors['company_house_number'] = __("Geen huisnummer bij bedrijf opgegeven", "planaday-api");
                }

                if (empty(sanitize_text_field($_POST['company_postal_code']))) {
                    $errors['company_postal_code'] = __("Geen postcode bij bedrijf opgegeven", "planaday-api");
                }

                if (empty(sanitize_text_field($_POST['company_email']))) {
                    $errors['company_email'] = __("Geen emailadres bij bedrijf opgegeven", "planaday-api");
                }

                if (empty(sanitize_text_field($_POST['company_city']))) {
                    $errors['company_city'] = __("Geen plaats bij bedrijf opgegeven", "planaday-api");
                }

                if (isset($this->_options['toonformphonenumbercompany'], $this->_options['toonformphonenumbercompanymanatory'])
                    && $this->_options['toonformphonenumbercompany'] === '1'
                    && $this->_options['toonformphonenumbercompanymanatory'] === '1'
                    && empty(sanitize_text_field($_POST['company_phonenumber']))) {
                    $errors['company_phonenumber'] = __("Telefoonnummer bij bedrijf is verplicht", "planaday-api");
                }

                if (isset($this->_options['toonemailinvoice'], $this->_options['toonemailinvoicemanatory'])
                    && $this->_options['toonemailinvoice'] === '1'
                    && $this->_options['toonemailinvoicemanatory'] === '1'
                    && empty(sanitize_text_field($_POST['invoice_email']))) {
                    $errors['invoice_email'] = __("Emailadres voor facturatie bij bedrijf is verplicht", "planaday-api");
                }
            }

            if (array_key_exists('attributes', $data)
                && (isset($this->_options['toonapiattributen'])
                    && $this->_options['toonapiattributen'] === '1')) {
                $attributes = $data['attributes'];
            }

            $amount = (int)sanitize_text_field($_POST['amount']);
            for ($i = 0; $i <= $amount; $i++) {
                if (isset($attributes)) {
                    foreach ($attributes as $akey => $aval) {
                        if ($aval['is_required']) {
                            if (!isset($_POST['attributes']) || !array_key_exists($aval['code'], $_POST['attributes'])) {
                                $errors[$aval['code']] = $akey . " is verplicht.";
                            }
                        }
                    }
                }

                if (empty(sanitize_text_field($_POST['first_name-' . $i]))) {
                    $errors['first_name-' . $i] = __("Geen voornaam opgegeven bij cursist ", "planaday-api") . ($i + 1);
                }

                if (empty(sanitize_text_field($_POST['last_name-' . $i]))) {
                    $errors['last_name-' . $i] = __("Geen achternaam opgegeven bij cursist ", "planaday-api") . ($i + 1);
                }

                if (empty(sanitize_text_field($_POST['email-' . $i]))) {
                    $errors['email-' . $i] = __("Geen email opgegeven bij cursist ", "planaday-api") . ($i + 1);
                }

                if (!is_email(sanitize_text_field($_POST['email-' . $i]))) {
                    $errors['email-' . $i] = __("Email is niet juist, vul een juist emailadres in bij cursist ",
                            "planaday-api") . ($i + 1);
                }

                if (isset($this->_options['toonformphonenumber'], $this->_options['toonformphonenumbermanatory'])
                    && $this->_options['toonformphonenumber'] === '1'
                    && $this->_options['toonformphonenumbermanatory'] === '1'
                    && empty(sanitize_text_field($_POST['phonenumber-' . $i]))) {
                    $errors['phonenumber-' . $i] = __("Telefoonnummer is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if (isset($this->_options['vraagcostcentercode'], $this->_options['vraagcostcentercodemanatory'])
                    && $this->_options['vraagcostcentercode'] === '1'
                    && $this->_options['vraagcostcentercodemanatory'] === '1'
                    && empty(sanitize_text_field($_POST['costcentercode-' . $i]))) {
                    $errors['costcentercode-' . $i] = __("Kostenplaats is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if (((isset($this->_options['toonformdateofbirth'], $this->_options['toonformdateofbirthmanatory'])
                            && $this->_options['toonformdateofbirth'] === '1'
                            && $this->_options['toonformdateofbirthmanatory'] === '1') || (isset($data['has_stap']) && $data['has_stap']))
                    && empty(sanitize_text_field($_POST['date_of_birth-' . $i]))) {
                    $errors['date_of_birth-' . $i] = __("Geboortedatum is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if (isset($this->_options['toonformcountryofbirth'], $this->_options['toonformcountryofbirthmanatory'])
                    && $this->_options['toonformcountryofbirth'] === '1'
                    && $this->_options['toonformcountryofbirthmanatory'] === '1'
                    && empty(sanitize_text_field($_POST['hometown-' . $i]))) {
                    $errors['hometown-' . $i] = __("Geboorteplaats is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if (isset($this->_options['toonformpositionstudent'], $this->_options['toonformpositionstudentmanatory'])
                    && $this->_options['toonformpositionstudent'] === '1'
                    && $this->_options['toonformpositionstudentmanatory'] === '1'
                    && empty(sanitize_text_field($_POST['company_position-' . $i]))) {
                    $errors['company_position-' . $i] = __("Functie opgeven is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                /*
                 * Checken op verplichte waardes voor 'vrije velden'
                 */
                if (array_key_exists('freefields_required', $_POST) && array_key_exists('freefields_values-' . $i, $_POST)) {
                    foreach ($_POST['freefields_values-' . $i] as $freefield_id => $freefield_value) {
                        if (in_array($freefield_id, $_POST['freefields_required']) && empty($freefield_value)) {
                            $errors['pad-freefield-' . $freefield_id] = __("Veld mag niet leeg zijn ", "planaday-api");

                        }
                    }

                    foreach ($_POST['freefields_required'] as $required_id) {
                        if (!array_key_exists($required_id, $_POST['freefields_values-' . $i])) {
                            $errors['pad-freefield-' . $required_id] = __("Er moet een keuze gemaakt worden ", "planaday-api");
                        }
                    }
                }

                /*
                 * Indien 'vraagadrescursist' op 1 staat, dan zijn enkele velden verplicht
                 */
                if ((((isset($this->_options['vraagadrescursist'])
                            && $this->_options['vraagadrescursist'] === '1')) || (isset($data['has_stap']) && $data['has_stap']))
                    && empty(sanitize_text_field($_POST['address-' . $i]))) {
                    $errors['address-' . $i] = __("Adres is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if ((((isset($this->_options['vraagadrescursist'])
                            && $this->_options['vraagadrescursist'] === '1')) || (isset($data['has_stap']) && $data['has_stap']))
                    && empty(sanitize_text_field($_POST['house_number-' . $i]))) {
                    $errors['house_number-' . $i] = __("Huisnummer is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if ((((isset($this->_options['vraagadrescursist'])
                            && $this->_options['vraagadrescursist'] === '1')) || (isset($data['has_stap']) && $data['has_stap']))
                    && empty(sanitize_text_field($_POST['postal_code-' . $i]))) {
                    $errors['postal_code-' . $i] = __("Postcode is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }

                if ((((isset($this->_options['vraagadrescursist'])
                            && $this->_options['vraagadrescursist'] === '1')) || (isset($data['has_stap']) && $data['has_stap']))
                    && empty(sanitize_text_field($_POST['city-' . $i]))) {
                    $errors['city-' . $i] = __("Woonplaats is verplicht bij cursist ", "planaday-api") . ($i + 1);
                }
                /*
                 * Indien 'vraagadrescursist' op 1 staat, dan zijn enkele velden verplicht
                 */

            }

            $attributelist = [];
            if (array_key_exists('attributes', $_POST)) {
                foreach ($_POST['attributes'] as $attributeCode => $attributeValue) {
                    $attributelist[] = [
                        "code" => $attributeCode,
                        "values" => is_array($attributeValue) ? $attributeValue : [$attributeValue],
                    ];
                }
            }

            if (count($errors) === 0) {
                if (isset($this->_options['toondebuginfo'])
                    && $this->_options['toondebuginfo'] === '1') {
                    echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__,
                        __LINE__, 'Geen errors in POST'));
                }

                for ($i = 0; $i <= $amount; $i++) {
					$new_date = null;
                    $original_date = $_POST['date_of_birth-' . $i] ?? null;
					if ($original_date !== null) {
						$new_date  = date( "Y-m-d", strtotime( $original_date ) );
					}

                    $student = [
                        "gender" => sanitize_text_field(idxVal($_POST['gender-' . $i])),
                        "first_name" => sanitize_text_field(idxVal($_POST['first_name-' . $i])),
                        "last_name" => sanitize_text_field(idxVal($_POST['last_name-' . $i])),
                        "maiden_name" => sanitize_text_field(idxVal($_POST['maiden_name-' . $i])),
                        "internal_reference" => sanitize_text_field(idxVal($_POST['internal_reference-' . $i])),
                        "nick_name" => sanitize_text_field(idxVal($_POST['nick_name-' . $i])),
                        "initials" => sanitize_text_field(idxVal($_POST['initials-' . $i])),
                        "prefix" => sanitize_text_field(idxVal($_POST['prefix-' . $i])),
                        "email" => sanitize_text_field(idxVal($_POST['email-' . $i])),
                        "address" => sanitize_text_field(idxVal($_POST['address-' . $i])),
                        "house_number" => sanitize_text_field(idxVal($_POST['house_number-' . $i])),
                        "house_number_extension" => sanitize_text_field(idxVal($_POST['house_number_extension-' . $i])),
                        "postal_code" => sanitize_text_field(idxVal($_POST['postal_code-' . $i])),
                        "city" => sanitize_text_field(idxVal($_POST['city-' . $i])),
                        "phonenumber" => sanitize_text_field(idxVal($_POST['phonenumber-' . $i])),
                        "date_of_birth" => sanitize_text_field(idxVal($new_date)),
                        "hometown" => sanitize_text_field(idxVal($_POST['hometown-' . $i])),
                        "employeeId" => sanitize_text_field(idxVal($_POST['employeeId-' . $i])),
                        "company_position" => sanitize_text_field(idxVal($_POST['company_position-' . $i])),
                        "remark" => sanitize_text_field(idxVal($_POST['remark-' . $i])),
                        "is_contact_person" => sanitize_text_field(isset($_POST['iscontactperson-' . $i])),
                        "costcentercode" => sanitize_text_field(idxVal($_POST['costcentercode-' . $i])),
                        "process_code95" => sanitize_text_field(isset($_POST['process_code95-' . $i])),
                        "process_soob" => sanitize_text_field(isset($_POST['process_soob-' . $i])),
                        "stap_regulation" => sanitize_text_field(isset($_POST['stap_regulation-' . $i])),
                    ];

                    if (array_key_exists('freefields_values-' . $i, $_POST)) {
                        $extrafields = [];
                        foreach ($_POST['freefields_values-' . $i] as $freefield_id => $freefield_value) {
                            $extrafields[$freefield_id] = null;
                            if (is_array($freefield_value)) {
                                $freefield_value = implode('|', $freefield_value);
                            }
                            $extrafields[$freefield_id] = sanitize_text_field($freefield_value);
                        }
                        $student["extrafields"] = $extrafields;
                    }
                    $studentdata[] = $student;
                }

                $companydata = [
                    "name" => sanitize_text_field(idxVal($_POST['company_name'])),
                    "email" => sanitize_text_field(idxVal($_POST['company_email'])),
                    "address" => sanitize_text_field(idxVal($_POST['company_address'])),
                    "house_number" => sanitize_text_field(idxVal($_POST['company_house_number'])),
                    "house_number_extension" => sanitize_text_field(idxVal($_POST['company_house_number_extension'])),
                    "postal_code" => sanitize_text_field(idxVal($_POST['company_postal_code'])),
                    "city" => sanitize_text_field(idxVal($_POST['company_city'])),
                    "phonenumber" => sanitize_text_field(idxVal($_POST['company_phonenumber'])),
                    "invoice_email" => sanitize_text_field(idxVal($_POST['invoice_email'])),
                ];

                if (isset($this->_options['vraagfinancieleinfobijbedrijf'])
                    && $this->_options['vraagfinancieleinfobijbedrijf'] === '1') {
                    $companydata["invoice"] = [
                        "addressee" => sanitize_text_field(idxVal($_POST['invoice_addressee'])),
                        "address" => sanitize_text_field(idxVal($_POST['invoice_address'])),
                        "house_number" => sanitize_text_field(idxVal($_POST['invoice_house_number'])),
                        "house_number_extension" => sanitize_text_field(idxVal($_POST['invoice_house_number_extension'])),
                        "postal_code" => sanitize_text_field(idxVal($_POST['invoice_postal_code'])),
                        "city" => sanitize_text_field(idxVal($_POST['invoice_city'])),
                        "country" => sanitize_text_field(idxVal($_POST['invoice_country'])),
                    ];
                }

                if (!empty($_POST['materials'])) {
                    $materials = idxVal($_POST['materials']);
                } else {
                    $materials = null;
                }

                $bookingdata = [
                    'students' => $studentdata,
                    'company' => $companydata,
                    'notes' => "Boeking via wordpress-website",
                    'created_at ' => Planaday_date::current_date(),
                    'creating_source' => $_SERVER['HTTP_HOST'],
                    'course_id' => sanitize_text_field($_POST['course_id']),
                    'materials' => $materials,
                    'attributes' => $attributelist
                ];

                if ($companychoice === 'nee' || (isset($data['has_stap']) && $data['has_stap'])) {
                    unset($bookingdata['company']);
                }

                if (isset($this->_options['onlybookingelparticulier']) && $this->_options['onlybookingelparticulier'] === '1') {
                    unset($bookingdata['company']);
                }

                if (empty($_POST['materials'])) {
                    unset($bookingdata['materials']);
                }
                if (empty($_POST['attributes'])) {
                    unset($bookingdata['attributes']);
                }
                if (isset($this->_options['toondebuginfo'])
                    && $this->_options['toondebuginfo'] === '1') {
                    echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__,
                        __LINE__, json_encode($bookingdata, JSON_PRETTY_PRINT)));
                }

                $costs = $data['costs']['user'] + ($data['costs']['user'] * ($data['costs']['vat'] / 100));
                $materialCostsTotal = 0;
                if (!empty($_POST['materials'])) {
                    $materialData = self::planaday_api_materialen_array($_POST['course_id']);
                    foreach ($_POST['materials'] as $materialId) {
                        foreach ($materialData as $material) {
                            if ((int)$materialId === (int)$material['id']) {
                                $materialCostsTotal += $material['selling_price'] + ($material['selling_price'] * ($material['vat'] / 100));
                            }
                        }
                    }
                }
                $costs += $materialCostsTotal;
                $totalCosts = number_format($costs, 2);

                $response = $this->planaday_api_do_booking($bookingdata);

                // Act on reponse
                $bookingOke = false;
                $payed = false;
                if (isset($response['result'])
                    && $response['result'] === 'OK') {
                    $bookingOke = true;

                    // Must there be payed?
                    if (isset($this->_options['betalingenactief'])
                        && $this->_options['betalingenactief'] === '1'
                        && settings_payment::planaday_is_betaling_mogelijk()
                        && $data['costs']['user'] !== 0) {
                        $this->planaday_api_redirect_to_payment($response['booking_id'], $_POST, $totalCosts, $data);
                        $payed = true;
                    } else {
                        $data = array_merge($_POST, ['booking_id' => $response['booking_id']]);

                        $this->planaday_api_send_mail_to_tutor(true, null, null, $data);

                        if (isset ($this->_options['mailcursusaanmeldingbedrijf'])
                            && $this->_options['mailcursusaanmeldingbedrijf'] === '1') {
                            $this->planaday_api_send_mail_to_company(true, null, null, $data);
                        }

                        if (isset ($this->_options['mailcursusaanmeldingcursist'])
                            && isset($data['email-0'])
                            && $data['email-0'] !== ''
                            && $this->_options['mailcursusaanmeldingcursist'] === '1') {
                            $this->planaday_api_send_mail_to_student(null, $data);
                        }

                        ob_start();
                        $this->planaday_api_redirect_or_text(true, null, $data);

                        return ob_get_clean();
                    }
                } else {
                    $this->planaday_api_send_mail_to_tutor(false, null, null, $_POST);
                }

                ob_start();
                $this->planaday_api_redirect_or_text($bookingOke, $payed, $_POST);

                return ob_get_clean();
            } else {
                ob_start();
                $msg = "<h4 id='errors'>" . __("Fouten gevonden", "planaday-api") . "</h4>";
                $msg .= "<p style='color: #ff0000;' class='pad-p'>" . __('De boeking kan helaas niet worden doorgevoerd, corrigeer de volgende velden:',
                        'planaday-api') . "</p>";
                $msg .= "<p class='pad-p'><ul id='pad-formerrors'>";
                foreach ($errors as $key => $value) {
                    $msg .= '<li style=\'color: #ff0000;\'>' . $value . '</li>';
                }
                $msg .= '</ul></p>';
                $this->planaday_api_bookingform($msg, $courseid, $errors);

                return ob_get_clean();
            }
        }

        $this->planaday_api_bookingform($msg, $courseid);

        return ob_get_clean();
    }

    private function planaday_api_redirect_to_payment(string $bookingId, array $postData, string $totalekosten, array $courseData)
    {
        $betaallink = get_home_url() . "/planadaybetaling/?referentie=" . sanitize_text_field($courseData['code']);
        $betaallink .= "&description=cursusboeken";
        $betaallink .= "&course_id=" . sanitize_text_field($postData['course_id']);
        $betaallink .= "&booking_id=" . sanitize_text_field($bookingId);
        $betaallink .= "&course_code=" . sanitize_text_field($courseData['code']);
        $betaallink .= "&course_name=" . sanitize_text_field($postData['course_name']);
        $betaallink .= "&course_startdate=" . sanitize_text_field($postData['course_startdate']);
        $betaallink .= "&company_name=" . sanitize_text_field($postData['company_name']);
        $betaallink .= "&company_address=" . sanitize_text_field($postData['company_address']);
        $betaallink .= "&company_postal_code=" . sanitize_text_field($postData['company_postal_code']);
        $betaallink .= "&company_house_number=" . sanitize_text_field($postData['company_house_number']);
        $betaallink .= "&company_city=" . sanitize_text_field($postData['company_city']);
        $betaallink .= "&company_phonenumber=" . sanitize_text_field($postData['company_phonenumber']);
        $betaallink .= "&company_email=" . sanitize_text_field($postData['company_email']);
        $betaallink .= "&first_name=" . sanitize_text_field($postData['first_name-0']);
        $betaallink .= "&last_name=" . sanitize_text_field($postData['last_name-0']);
        $betaallink .= "&email=" . sanitize_text_field(trim($postData['email-0']));
        if (isset($postData['date_of_birth-0'])) {
            $betaallink .= "&date_of_birth=" . sanitize_text_field($postData['date_of_birth-0']);
        }
        $betaallink .= "&amount=" . sanitize_text_field(str_replace(',', '', $totalekosten));
        echo '<script>location.replace("' . $betaallink . '");</script>';
    }

    /**
     * @param $args
     *
     * @return array|mixed|object|string
     */
    private function planaday_api_do_booking($args)
    {
        if (isset($this->_options['toondebuginfo']) && $this->_options['toondebuginfo'] === '1') {
            echo(sprintf("<PRE><br><br><br>File:%s<br>Line: %s<br><br>%s</PRE>", __FILE__, __LINE__,
                print_r($args, true)));
        }

        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('booking/%s', $args['course_id']),
            json_encode($args),
            'POST'
        );


        if (isset($this->_options['toondebuginfo'])
            && $this->_options['toondebuginfo'] === '1') {
            echo(sprintf("<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>", __FUNCTION__, __FILE__, __LINE__,
                print_r($data, true)));
        }

        if (idxVal($data['error']) !== null) {
            print "<div id=\"pad-errors\" style=\"display: block;\" role=\"alert\">Error opgetreden: " . $data['message'] . "</div>";
        }

        return $data;
    }

    /**
     * @param $msg
     * @param $courseid
     * @param $errors
     */
    private function planaday_api_bookingform($msg, $courseid, ?array $errors = [])
    {
        $pagename = get_query_var('pagename');
        $payment = get_option('planaday-api-payment');

        $client = client::planaday_api_get_instance();
        $data = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            sprintf('course/%s', $courseid),
            []
        );

        $bookingform = '<h3 id="cursustitel">' . $this->_options['teksttitelbooking'] . '</h3><a name="boeken"></a>';
        if (settings_payment::planaday_is_betaling_mogelijk()) {
            $bookingform .= $payment['toelichting'];
            if (!settings_payment::planaday_is_betaling_live()) {
                $bookingform .= "<br /><strong style='color: #ff0000;'>" . __("Let op: dit is een testbetaling",
                        "planaday-api") . "</strong>";
            }
        }
        $bookingform .= $msg;
        $startdate = Planaday_date::give_readable_date($this->planaday_api_dagdeel($data['dayparts'][0]['id'], "date"));
        $bookingform .= '<form action="' . $this->planaday_api_course_link($data['id'],
                $data['name']) . '" method="post" name="padbooking" id="padbooking">';
        $amount = (int)(isset($_POST['amount']) ? $_POST['amount'] + 1 : 1);
        $bookingform .= '<div style="display: none;">
            <input name="course_id" value="' . $courseid . '" type="hidden">
            <input name="amount" value="' . $amount . '" id="amount" type="hidden">
            <input name="course_name" value="' . $data['name'] . '" type="hidden">
            <input name="course_code" value="' . $data['code'] . '" type="hidden">
            <input name="course_startdate" value="' . $startdate . '" type="hidden">
            <input name="algemenevoorwaarden" value="nee" type="hidden">';
        if ((isset($data['has_stap']) && $data['has_stap']) && (isset($data['stap_only']) && $data['stap_only'])) {
            $bookingform .= '<input name="stap_regulation-0" value="true" type="hidden">';
        }
        $bookingform .= '</div>';

        if (isset($this->_options['materialbookingactive'])
            && $this->_options['materialbookingactive'] === '1'
            && $this->planaday_api_materialen_bij_cursus($courseid)) {
            $bookingform .= '<h4>' . $this->_options['materialtitle'] . '</h4>';
            $bookingform .= '<div id="materials">' . $this->planaday_api_materialen_checklist($courseid) . '</div><br />';
        }

        if ($_POST) {
            if ($_POST['companychoice'] === 'ja') {
                $voorkeurbedrijf = 'ja';
            } else {
                $voorkeurbedrijf = 'nee';
            }
        } else {
            if (isset($this->_options['voorkeurbooleancompany'])
                && $this->_options['voorkeurbooleancompany'] === '0') {
                $voorkeurbedrijf = 'ja';
            } else {
                $voorkeurbedrijf = 'nee';
            }
        }

        if (
            (!isset($data['has_stap']) || $data['has_stap'] === false) &&
            (isset($this->_options['onlybookingelparticulier']) && $this->_options['onlybookingelparticulier'] === '0')
        ) {
            $bookingform .= '<h4 id="pad-company-choice">' . __("Bedrijf/organisatie of particulier?", "planaday-api") . '</h4>';
            $bookingform .= '
<p class="pad-p"><div class="radio" id="company_choice">
  <label> <input type="radio" name="companychoice" id="companychoiceyes"  for="companychoiceyes" value="ja" ' . ($voorkeurbedrijf === 'ja' ? "checked" : "") . '> ' . __("Ja, graag inschrijven als bedrijf/organisatie",
                    "planaday-api") . ' </label> 
  <label> <input type="radio" name="companychoice" id="companychoiceno" for="companychoiceno" value="nee" ' . ($voorkeurbedrijf === 'nee' ? "checked" : "") . '> ' . __("Nee, ik ben particulier",
                    "planaday-api") . ' </label>
</div></p><div id="company_details" style="' . ($voorkeurbedrijf === 'nee' ? "display: none;" : "") . '">';

            if (isset($this->_options['onlybookingelparticulier']) && $this->_options['onlybookingelparticulier'] === '0') {
                $bookingform .= '<h4 id="pad-company">' . __("Bedrijfsgegevens", "planaday-api") . '</h4>';
            }

            $bookingform .= '';

            $bookingform .= '<label id="pad-company_name" for="company_name"> ' . __("Bedrijfsnaam", "planaday-api") . ' *<br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield("company_name",
                sanitize_text_field($_POST['company_name'] ?? null), "Bedrijfsnaam", 100,
                isset($errors['company_name']));
            $bookingform .= '</span> </label>';

            $bookingform .= '<div class="pad-street-block">';
            $bookingform .= '<label id="pad-street" for="company_address"> ' . __("Straat & huisnummer", "planaday-api") . ' *<br>';
            $bookingform .= $this->planaday_api_inputfield("company_address",
                    sanitize_text_field(isset($_POST["company_address"]) ? $_POST['company_address'] : null), "Straat", 75,
                    isset($errors["company_address"])) . ' &nbsp;';
            $bookingform .= $this->planaday_api_inputfield("company_house_number",
                sanitize_text_field(isset($_POST["company_house_number"]) ? $_POST['company_house_number'] : null), "Huisnr", 18,
                isset($errors["company_house_number"]));
            $bookingform .= $this->planaday_api_inputfield("company_house_number_extension",
                sanitize_text_field(isset($_POST["company_house_number_extension"]) ? $_POST['company_house_number_extension'] : null),
                "Huisnr ext", 18, isset($errors["company_house_number_extension"]));
            $bookingform .= '</label> </div> ';

            $bookingform .= '<div class="pad-address-info">';
            $bookingform .= '<label id="pad-company_postal_code" for="company_postal_code"> ' . __("Postcode & plaats",
                    "planaday-api") . ' *<br>';
            $bookingform .= $this->planaday_api_inputfield("company_postal_code",
                    sanitize_text_field($_POST['company_postal_code'] ?? null), "Postcode", 28,
                    isset($errors["company_postal_code"])) . ' &nbsp;';
            $bookingform .= $this->planaday_api_inputfield("company_city",
                sanitize_text_field($_POST['company_city'] ?? null), "Plaats", 65,
                isset($errors['company_city']));
            $bookingform .= ' </label> </div> ';

            $bookingform .= '<label id="pad-emailadres-bedrijf" for="company_email"> ' . __("Emailadres (bedrijf)",
                    "planaday-api") . ' * <br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield("company_email",
                sanitize_text_field($_POST['company_email'] ?? null), "Emailadres", 100,
                isset($errors['company_email']));
            $bookingform .= '</span> </label>';

            if (isset($this->_options['toonemailinvoice'])
                && $this->_options['toonemailinvoice'] === '1') {
                $bookingform .= '<br><label id="pad-invoicemail" for="pad-invoicemail"> ' . __("Emailadres facturatie",
                        "planaday-api") . ' ';
                if (isset($this->_options['toonemailinvoicemanatory'])
                    && $this->_options['toonemailinvoicemanatory'] === '1') {
                    $bookingform .= ' * ';
                }
                $bookingform .= '<br><span>';
                $bookingform .= $this->planaday_api_inputfield("invoice_email",
                    sanitize_text_field($_POST['invoice_email'] ?? null), "Emailadres facturatie", 100,
                    isset($errors['invoice_email']));
                $bookingform .= '</span> </label>';
            }

            if (isset($this->_options['vraagfinancieleinfobijbedrijf'])
                && $this->_options['vraagfinancieleinfobijbedrijf'] === '1') {
                $bookingform .= '<br><h4 id="pad-invoice"> ' . __("Factuuradres", "planaday-api") . ' </h4>';
                $bookingform .= '<br><label id="pad-invoice-street" for="pad-invoice-street"> ' . __("Straat & huisnummer",
                        "planaday-api") . ' <br>';
                $bookingform .= '<span>';
                $bookingform .= $this->planaday_api_inputfield("invoice_addressee",
                        sanitize_text_field(isset($_POST["invoice_addressee"]) ? $_POST['invoice_addressee'] : null), "Naam", 75,
                        isset($errors["invoice_addressee"])) . ' &nbsp;';
                $bookingform .= $this->planaday_api_inputfield("invoice_address",
                        sanitize_text_field(isset($_POST["invoice_address"]) ? $_POST['invoice_address'] : null), "Straat", 75,
                        isset($errors["invoice_address"])) . ' &nbsp;';
                $bookingform .= $this->planaday_api_inputfield("invoice_house_number",
                    sanitize_text_field(isset($_POST["invoice_house_number"]) ? $_POST['invoice_house_number'] : null), "Huisnr", 18,
                    isset($errors["invoice_house_number"]));
                $bookingform .= $this->planaday_api_inputfield("invoice_house_number_extension",
                    sanitize_text_field(isset($_POST["invoice_house_number_extension"]) ? $_POST['invoice_house_number_extension'] : null),
                    "Huisnr ext", 18, isset($errors["invoice_house_number_extension"]));
                $bookingform .= '</span> </label>';

                $bookingform .= '<br><label id="pad-invoice-company_postal_code" for="pad-invoice-company_postal_code"> ' . __("Postcode & plaats",
                        "planaday-api") . ' <br>';
                $bookingform .= '<span>';
                $bookingform .= $this->planaday_api_inputfield("invoice_postal_code",
                        sanitize_text_field($_POST['invoice_postal_code'] ?? null), "Postcode", 28,
                        isset($errors["invoice_postal_code"])) . ' &nbsp;';
                $bookingform .= $this->planaday_api_inputfield("invoice_city",
                    sanitize_text_field($_POST['invoice_city'] ?? null), "Plaats", 65,
                    isset($errors['invoice_city']));
                $bookingform .= $this->planaday_api_inputfield("invoice_country",
                    sanitize_text_field($_POST['invoice_country'] ?? null), "Land", 65,
                    isset($errors['invoice_country']));
                $bookingform .= ' <br></span> </label>';

            }

            if (isset($this->_options['toonformphonenumbercompany'])
                && $this->_options['toonformphonenumbercompany'] === '1') {
                $bookingform .= '<label id="pad-telefoon-bedrijf" for="pad-telefoon-bedrijf"> ' . __("Telefoonnummer",
                        "planaday-api") . ' ';
                if (isset($this->_options['toonformphonenumbercompanymanatory'])
                    && $this->_options['toonformphonenumbercompanymanatory'] === '1') {
                    $bookingform .= ' * ';
                }
                $bookingform .= '<br><span>';
                $bookingform .= $this->planaday_api_inputfield("company_phonenumber",
                    sanitize_text_field($_POST['company_phonenumber'] ?? null), "Telefoonnummer", 100,
                    isset($errors['company_phonenumber']));
                $bookingform .= '<br></span> </label>';
            }

            $bookingform .= '</div>';
        }

        if (array_key_exists('attributes', $data)
            && (isset($this->_options['toonapiattributen'])
                && $this->_options['toonapiattributen'] === '1')) {
            $bookingform .= '<div id="pad-api-attributes">';
            $bookingform .= '<h4 class="pad-api-attribute-title">' . __("Extra vragen", "planaday-api") . ':</h4>';
            $attributes = $data['attributes'];
            foreach ($attributes as $akey => $aval) {
                $values = $aval['values'];
                $required = null;
                if ($aval['is_required']) {
                    $required = " <span title='" . __('verplicht veld/keuze', 'planaday-api') . "'> * </span> ";
                }
                if ($aval['for_booking']) {
                    if ($aval['multiple_values_allowed']) {
                        $bookingform .= '<h5 class="pad-api-attribute">' . $akey . $required . '</h5>';
                        $bookingform .= '<fieldset class="pad-api-attribute-fieldset"">';
                        foreach ($values as $vkey => $vval) {
                            $bookingform .= '<label for="' . $aval['code'] . '"><input type="checkbox" name="attributes[' . $aval['code'] . '][]" id="' . $aval['code'] . '" value="' . $vkey . '"> ' . $vkey . '</label>';
                        }
                        $bookingform .= '</fieldset>';
                    } else {
                        $bookingform .= '<h5 class="pad-api-attribute">' . $akey . $required . '</h5>';
                        $bookingform .= '<fieldset class="pad-api-attribute-fieldset"">';
                        foreach ($values as $vkey => $vval) {
                            $bookingform .= '<label for="' . $aval['code'] . '"><input type="radio" name="attributes[' . $aval['code'] . ']" id="' . $aval['code'] . '" value="' . $vkey . '"> ' . $vkey . '</label>';
                        }
                        $bookingform .= '</fieldset>';
                    }
                }
            }
            $bookingform .= '</div>';
        }

        $bookingform .= '<h3>' . __("Persoonsgegevens", "planaday-api") . '</h3>';

        $bookingform .= '<div id="extraPersonTemplate" class="extraPersonTemplate">';
        $bookingform .= '<h4>' . __("Deelnemer", "planaday-api") . '</h4>';
        if (!array_key_exists('attributes', $data)
            && (isset($this->_options['betalingenactief']) && $this->_options['betalingenactief'] === '0')
            && (!array_key_exists('has_stap', $data) || $data['has_stap'] !== true)
            && (!is_array($extrafields) || count($extrafields) === 0)
        ) {
            $bookingform .= '<p class="pad-p"><button onClick="removeRow(event)" class="removeRow"><i class="fas fa-remove fa-fw"></i> ' . __("Verwijder deze deelnemer",
                    "planaday-api") . '</button></p>';
        }
        $bookingform .= '<div id="pad-name-block" class="pad-name-block">';
        $bookingform .= '<label class="pad-naam"> ' . __("Geslacht, initialen, voor- en achternaam", "planaday-api") . ' *<br>';
        $bookingform .= '<label for="gender"><select name="gender" id="gender" class="pad-geslacht">
<option value="m">Dhr</option>
<option value="f">Mevr</option>
<option value="nvt">Nvt</option>
<option value="null" selected>-</option>
</select></label> ';

        $bookingform .= $this->planaday_api_inputfield(
                "initials",
                $_POST['initials'] ?? null,
                "Initialen",
                12,
                isset($errors["initials"]),
                false
            ) . ' &nbsp;';
        $bookingform .= $this->planaday_api_inputfield(
                "first_name",
                $_POST['first_name'] ?? null,
                "Voornaam",
                36,
                isset($errors["first_name"])
            ) . ' &nbsp;';
        if (isset($this->_options['vraagroepnaam'])
            && $this->_options['vraagroepnaam'] === '1') {
            $bookingform .= $this->planaday_api_inputfield(
                    "nick_name",
                    $_POST['nick_name'] ?? null,
                    __("Roepnaam", "planaday-api"),
                    36,
                    isset($errors["nick_name"])
                ) . ' &nbsp;';
        }
        $bookingform .= $this->planaday_api_inputfield(
                "prefix",
                $_POST['prefix'] ?? null,
                "Tussenvoegsel",
                12,
                isset($errors["prefix"])
            ) . ' &nbsp;';
        $bookingform .= $this->planaday_api_inputfield(
                "last_name",
                $_POST['last_name'] ?? null,
                "Achternaam",
                36,
                isset($errors["last_name"])
            ) . ' &nbsp;';
        if (isset($this->_options['vraagmeisjesnaam'])
            && $this->_options['vraagmeisjesnaam'] === '1') {
            $bookingform .= $this->planaday_api_inputfield(
                    "maiden_name",
                    $_POST['maiden_name'] ?? null,
                    __("Meisjesnaam", "planaday-api"),
                    36,
                    isset($errors["maiden_name"])
                ) . ' &nbsp;';
        }
        $bookingform .= '</label> </div>';

        if ((isset($this->_options['vraagadrescursist']) && $this->_options['vraagadrescursist'] === '1')
            || (isset($data['has_stap']) && $data['has_stap'])) {
            $bookingform .= '<div class="pad-street-block">';
            $bookingform .= '<label class="pad-cursist-straat" for="street"> ' . __("Straat & huisnummer", "planaday-api") . ' *<br>';
            $bookingform .= $this->planaday_api_inputfield(
                    "address",
                    $_POST['address'] ?? null,
                    __("Straat", "planaday-api"),
                    36,
                    isset($errors["address"])
                ) . ' &nbsp;';
            $bookingform .= $this->planaday_api_inputfield(
                "house_number",
                $_POST['house_number'] ?? null,
                __("Huisnr", "planaday-api"),
                36,
                isset($errors["house_number"])
            );

            if ((isset($this->_options['vraaghuisnrext']) && $this->_options['vraaghuisnrext'] === '1')
                || (isset($data['has_stap']) && $data['has_stap'])) {
                $bookingform .= $this->planaday_api_inputfield(
                    "house_number_extension",
                    $_POST['house_number_extension'] ?? null,
                    __("Huisnr ext", "planaday-api"),
                    36,
                    isset($errors["house_number_extension"])
                );
            }

            $bookingform .= '</label> </div>';
            $bookingform .= '<div class="pad-address-info">';
            $bookingform .= '<label for="address-info" class="pad-cursist-postcode"> ' . __("Postcode & plaats",
                    "planaday-api") . ' *<br>';

            $bookingform .= $this->planaday_api_inputfield(
                    "postal_code",
                    $_POST['postal_code'] ?? null,
                    __("Postcode", "planaday-api"),
                    36,
                    isset($errors["postal_code"])
                ) . ' &nbsp;';

            $bookingform .= $this->planaday_api_inputfield(
                "city",
                $_POST['city'] ?? null,
                __("Plaats", "planaday-api"),
                36,
                isset($errors["city"])
            );
            $bookingform .= '</label> </div>';
        }

        $bookingform .= '<div class="pad-emailadres-cursist">';
        $bookingform .= '<label for="pad-email-cursist" class="pad-email-cursist"> ' . __("Emailadres", "planaday-api") . ' *<br>';
        $bookingform .= '<span>';
        $bookingform .= $this->planaday_api_inputfield(
            "email",
            $_POST['email'] ?? null,
            __("Emailadres", "planaday-api"),
            100,
            isset($errors['email']),
            false,
            'email'
        );
        $bookingform .= '</span> </label>';
        $bookingform .= '</div>';

        if (isset($this->_options['toonformphonenumber'])
            && $this->_options['toonformphonenumber'] === '1') {
            $bookingform .= '<div class="pad-telefoonnummer-cursist">';
            $bookingform .= '<label for="pad-telefoon-cursist" class="pad-telefoon-cursist"> ' . __("Telefoonnummer", "planaday-api");

            if (isset($this->_options['toonformphonenumbermanatory'])
                && $this->_options['toonformphonenumbermanatory'] === '1') {
                $bookingform .= ' * ';
            }

            $bookingform .= '<br><span>';
            $bookingform .= $this->planaday_api_inputfield(
                "phonenumber",
                $_POST['phonenumber'] ?? null,
                __("Telefoonnummer", "planaday-api"),
                100,
                isset($errors['phonenumber'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if ((isset($this->_options['toonformdateofbirth'])
                && $this->_options['toonformdateofbirth'] === '1') || (isset($data['has_stap']) && $data['has_stap'])) {
            $bookingform .= '<div class="pad-geboortedatum-cursist">';
            $bookingform .= '<label for="pad-geboortedatum" class="pad-geboortedatum"> ' . __("Geboortedatum", "planaday-api");

            if ((isset($this->_options['toonformdateofbirthmanatory'])
                    && $this->_options['toonformdateofbirthmanatory'] === '1') || (isset($data['has_stap']) && $data['has_stap'])) {
                $bookingform .= ' * ';
            }

            $bookingform .= '<br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield(
                "date_of_birth",
                $_POST['date_of_birth'] ?? null,
                "(dd-mm-jjjj)",
                100,
                isset($errors['date_of_birth'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['toonformcountryofbirth'])
            && $this->_options['toonformcountryofbirth'] === '1') {
            $bookingform .= '<div class="pad-geboorteplaats-cursist">';
            $bookingform .= '<label for="pad-geboorteplaats" class="pad-geboorteplaats"> ' . __("Geboorteplaats", "planaday-api");

            if (isset($this->_options['toonformcountryofbirthmanatory'])
                && $this->_options['toonformcountryofbirthmanatory'] === '1') {
                $bookingform .= ' * ';
            }

            $bookingform .= '<br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield(
                "hometown",
                $_POST['hometown'] ?? null,
                __("Geboorteplaats", "planaday-api"),
                100,
                isset($errors['hometown'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['toonformpositionstudent'])
            && $this->_options['toonformpositionstudent'] === '1') {
            $bookingform .= '<div class="pad-functie-cursist">';
            $bookingform .= '<label for="pad-functie" class="pad-functie"> ' . __("Functie", "planaday-api");

            if (isset($this->_options['toonformpositionstudentmanatory'])
                && $this->_options['toonformpositionstudentmanatory'] === '1') {
                $bookingform .= ' * ';
            }
            $bookingform .= '<br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield(
                "company_position",
                $_POST['company_position'] ?? null,
                __("Functie", "planaday-api"),
                100,
                isset($errors['company_position'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['vraagcostcentercode'])
            && $this->_options['vraagcostcentercode'] === '1') {
            $bookingform .= '<div class="pad-kostenplaats-cursist">';
            $bookingform .= '<label for="pad-kostenplaats" class="pad-kostenplaats"> ' . __($this->_options['tekstkostenplaats'],
                    "planaday-api") . ' ';

            if ((isset($this->_options['vraagcostcentercode'])
                && $this->_options['vraagcostcentercodemanatory'] === '1')) {
                $bookingform .= ' * ';
            }

            $bookingform .= '<br>';
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_inputfield(
                "costcentercode",
                $_POST['costcentercode'] ?? null,
                __($this->_options['tekstkostenplaats'], "planaday-api"),
                100,
                isset($errors['costcentercode'])
            );
            $bookingform .= '</span><br />';
            $bookingform .= __('Let op: deze kostenplaats moet bekend zijn bij de opleider.', 'planaday-api');
            $bookingform .= '<br /> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['vraagpersoneelsnummer'])
            && $this->_options['vraagpersoneelsnummer'] === '1') {
            $bookingform .= '<div class="pad-personeelsnummer-cursist">';
            $bookingform .= '<label for="pad-personeelsnummer" class="pad-personeelsnummer"> ' . __("Personeelsnummer",
                    "planaday-api");
            $bookingform .= '<br><span>';
            $bookingform .= $this->planaday_api_inputfield(
                "employeeId",
                $_POST['employeeId'] ?? null,
                __("Personeelsnummer", "planaday-api"),
                100,
                isset($errors['employeeId'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['vraag_internal_reference'])
            && $this->_options['vraag_internal_reference'] === '1') {
            $bookingform .= '<div class="pad-internal_reference-cursist">';
            $bookingform .= '<label for="pad-internal_reference" class="pad-internal_reference"> ' . __("Interne referentie",
                    "planaday-api");
            $bookingform .= '<br><span>';
            $bookingform .= $this->planaday_api_inputfield(
                "internal_reference",
                $_POST['internal_reference'] ?? null,
                __("Interne referentie", "planaday-api"),
                100,
                isset($errors['internal_reference'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        if (isset($this->_options['toonstudentremark'])
            && $this->_options['toonstudentremark'] === '1') {
            $bookingform .= '<div class="pad-opmerking-cursist">';
            $bookingform .= '<br><label for="pad-opmerking" class="pad-opmerking"> ' . __("Opmerking", "planaday-api");
            $bookingform .= '<span>';
            $bookingform .= $this->planaday_api_textfield(
                "remark",
                $_POST['remark'] ?? null,
                __("Opmerking", "planaday-api"),
                100,
                isset($errors['remark'])
            );
            $bookingform .= '</span> </label>';
            $bookingform .= '</div>';
        }

        /* Voorkeur 0 = company / Voorkeur 1 = particulier */
        if (isset($this->_options['vraagcontactpersoon'])
            && $this->_options['vraagcontactpersoon'] === '1') {
            $bookingform .= '<div class="contactdiv" style="' . ($voorkeurbedrijf === "ja" ? "display: none;" : "") . '">';
            $bookingform .= '<label for="iscontactperson">
            <input type="checkbox" name="iscontactperson" id="iscontactperson" class="iscontactperson">
             ' . __("Ja, is ook contactpersoon voor dit bedrijf", "planaday-api") . '  
            </label><br /><br /></div>';
        }

        if (isset($data['has_stap']) && $data['has_stap'] === true) {
            $bookingform .= '<div class="stapquestion">';
            if (isset($data['stap_only'])
                && $data['stap_only'] === true) {
                $bookingform .= '<label for="stap_regulation" id="stap_regulation">
            <input type="checkbox" name="stap_regulation" checked disabled id="stap_regulation" class="stap_regulation"> ';
                $bookingform .= __("Deze cursus wordt gegeven voor de STAP regeling. Je wordt hier automatisch voor aangemeld",
                    "planaday-api");
            } else {
                $bookingform .= '<label id="stap_regulation" for="stap_regulation">
            <input type="checkbox" name="stap_regulation" id="stap_regulation" class="stap_regulation" value=""> ';
                $bookingform .= __("Deze cursus wordt ook gegeven met de STAP regeling. Wil je je hiervoor aanmelden?", "planaday-api");
            }
            $bookingform .= ' </label><br /><br /></div>';
        }

        if (isset($this->_options['toonoptiecode95'], $data['has_code95'])
            && $this->_options['toonoptiecode95'] === '1'
            && $data['has_code95'] === true) {
            $bookingform .= '<div class="code95question">';
            $bookingform .= '<label for="process_code95">
            <input type="checkbox" name="process_code95" id="process_code95" class="process_code95"> ';
            $bookingform .= __($this->_options['tekstcode95optie'], "planaday-api");
            $bookingform .= ' </label><br /><br /></div>';
        }

        if (isset($this->_options['toonoptiesoob'], $data['has_soob'])
            && $this->_options['toonoptiesoob'] === '1'
            && $data['has_soob'] === true) {
            $bookingform .= '<div class="soobquestion">';
            $bookingform .= '<label for="process_soob">
            <input type="checkbox" name="process_soob" id="process_soob" class="process_soob"> ';
            $bookingform .= __($this->_options['tekstsooboptie'], "planaday-api");
            $bookingform .= ' </label><br /><br /></div>';
        }
        $bookingform .= '</div>';

        $bookingform .= '<div id="container"></div>';
        $bookingform .= '<div id="pad-verplicht">' . __("Velden met * zijn verplicht voor het op de juiste manier verwerken van jouw aanvraag",
                "planaday-api") . '.</div>';

        $extrafields = $client->call(
            $this->_options['url'],
            $this->_options['key'],
            'extrafields/list',
            []
        );
        if (array_key_exists('extrafields', $extrafields)) {
            $extrafields = $extrafields['extrafields'];
        }

        if (count($extrafields) === 1
            && array_key_exists('student', $extrafields)
            && !$extrafields['student']) {
            unset($extrafields['student']);
        }

        if (is_array($extrafields) && count($extrafields) > 0) {
            $bookingform .= '<div id="pad-freefields">';
            $bookingform .= '<h4 class="pad-freefield-title">' . __("Aanvullende vragen", "planaday-api") . ':</h4>';
            foreach ($extrafields['student'] as $akey => $aval) {
                $required = null;
                $values = $aval['options'];
                $defaultvalue = $aval['default_value'];
                if ($aval['is_required']) {
                    $required = " <span title='" . __('verplicht veld/keuze', 'planaday-api') . "'> * </span> ";
                    $bookingform .= "<input type='hidden' name='freefields_required[]' value='" . $aval['id'] . "'>";
                }
                $bookingform .= '<div id="pad-freefield-' . $aval['id'] . '" class="pad-freefield-block">';
                $bookingform .= '<h5 class="pad-freefield-title';
                if (isset($errors['pad-freefield-' . $aval['id']])) {
                    $bookingform .= ' formerrorlabel ';
                }
                $bookingform .= '">' . $aval['name'] . $required . '</h5>';
                if (!empty($aval['description'])) {
                    $bookingform .= '<h6 class="pad-freefield-description">' . $aval['description'] . '</h6>';
                }
                if ($aval['type'] === 'textfield') {
                    $bookingform .= '<fieldset class="pad-freefield-fieldset">';
                    $bookingform .= '<label for="option_' . $vkey . '_' . $akey . '">';
                    $bookingform .= '<input type="text" name="freefields_values-0[' . $aval['id'] . ']" id="option_' . $vkey . '_' . $akey . '" value="' . $defaultvalue . '"';
                    if (isset($errors['pad-freefield-' . $aval['id']])) {
                        $bookingform .= " class='formerror' ";
                    }
                    $bookingform .= '></label>';
                    $bookingform .= '</fieldset></div>';
                }
                if ($aval['type'] === 'checkbox') {
                    $defaultvalue = explode('|', $defaultvalue);
                    $bookingform .= '<fieldset class="pad-freefield-fieldset">';
                    foreach ($values as $vkey => $vval) {
                        $bookingform .= '<label for="option_' . $vkey . '_' . $akey . '"';
                        if (isset($errors['pad-freefield-' . $aval['id']])) {
                            $bookingform .= ' class="formerrorlabel" ';
                        }
                        $bookingform .= '><input type="checkbox" name="freefields_values-0[' . $aval['id'] . '][]" id="option_' . $vkey . '_' . $akey . '" value="' . $vval . '" ';
                        if (is_array($defaultvalue) && in_array($vval, $defaultvalue)) {
                            $bookingform .= ' checked="checked" ';
                        }
                        $bookingform .= '> ' . $vval . '</label><br />';
                    }
                    $bookingform .= '</fieldset></div>';
                }
                if ($aval['type'] === 'selectbox' || $aval['type'] === 'radiobutton') {
                    $bookingform .= '<fieldset class="pad-freefield-fieldset">';
                    foreach ($values as $vkey => $vval) {
                        $bookingform .= '<label for="option_' . $vkey . '_' . $akey . '"';
                        if (isset($errors['pad-freefield-' . $aval['id']])) {
                            $bookingform .= ' class="formerrorlabel" ';
                        }
                        $bookingform .= '><input type="radio" name="freefields_values-0[' . $aval['id'] . ']" id="option_' . $vkey . '_' . $akey . '" value="' . $vval . '" ';
                        if (!empty($defaultvalue) && $vval === $defaultvalue) {
                            $bookingform .= 'checked="checked"';
                        }
                        $bookingform .= '> ' . $vval . '</label><br />';
                    }
                    $bookingform .= '</fieldset></div>';
                }
            }
            $bookingform .= '</div>';
        }

        if ($voorkeurbedrijf === 'nee') {
            $bookingform .= '<div id="button_extra_student" style="display: none;">';
        } else {
            $bookingform .= '<div id="button_extra_student">';
        }

        if (!array_key_exists('attributes', $data)
            && (isset($this->_options['betalingenactief']) && $this->_options['betalingenactief'] === '0')
            && (!array_key_exists('has_stap', $data) || $data['has_stap'] !== true)
            && (!is_array($extrafields) || count($extrafields) === 0)
        ) {
            $bookingform .= '<p class="pad-p"><button type="button" id="addRow"><i class="fas fa-plus-square fa-fw"></i> ' . __("Voeg een deelnemer toe",
                    "planaday-api") . '</button></p>';
        } else {
            $bookingform .= '<p class="pad-p">' . __('Het is niet mogelijk om meerdere deelnemers toe te voegen', 'planaday-api') . '</p>';
        }
        $bookingform .= '</div>';

        if (isset($this->_options['toonformalgemenevoorwaarden'])
            && $this->_options['toonformalgemenevoorwaarden'] === '1') {
            if ($_POST
                && $_POST['algemenevoorwaarden'] === 'ja') {
                $avchecked = "checked";
            } else {
                $avchecked = "";
            }
            $bookingform .= '<div id="algemenevoorwaarden">';
            $bookingform .= '<input type="checkbox" id="algemenevoorwaarden-check" name="algemenevoorwaarden" value="ja" ' . $avchecked . '><label for="algemenevoorwaarden-check"> &nbsp;';
            $bookingform .= __("Ik ga akkoord met de algemene voorwaarden",
                    "planaday-api") . ' (<a href="' . $this->_options['urlalgemenevoorwaarden'] . '" target="_blank">' . __("hier",
                    "planaday-api") . '</a> ' . __("kun je ze lezen", "planaday-api") . ').</label></div>';
        }

        $bookingform .= '<p class="pad-p"><button type="submit" id="pad-submitbuttton" class="pad-submitbuttton">' . __("Verstuur aanvraag",
                "planaday-api") . '</button><span class="ajax-loader"></span></p>';
        $bookingform .= '</form>';
        $bookingform .= '</br>';

        $studentForms = '';
        for ($i = 0; $i < $amount; $i++) {
            $studentForms .= 'jQuery("<div/>", {"class": "extraPerson", html: GetHtml()}).appendTo("#container");' . PHP_EOL;
            $studentForms .= '
               jQuery(\'#initials-' . $i . '\').val("' . (isset($_POST['initials-' . $i]) ? $_POST['initials-' . $i] : '') . '");
               jQuery(\'#first_name-' . $i . '\').val("' . (isset($_POST['first_name-' . $i]) ? $_POST['first_name-' . $i] : '') . '");
               jQuery(\'#last_name-' . $i . '\').val("' . (isset($_POST['last_name-' . $i]) ? $_POST['last_name-' . $i] : '') . '");
               jQuery(\'#maiden_name-' . $i . '\').val("' . (isset($_POST['maiden_name-' . $i]) ? $_POST['maiden_name-' . $i] : '') . '");
               jQuery(\'#nick_name-' . $i . '\').val("' . (isset($_POST['nick_name-' . $i]) ? $_POST['nick_name-' . $i] : '') . '");
               jQuery(\'#address-' . $i . '\').val("' . (isset($_POST['address-' . $i]) ? $_POST['address-' . $i] : '') . '");
               jQuery(\'#house_number-' . $i . '\').val("' . (isset($_POST['house_number-' . $i]) ? $_POST['house_number-' . $i] : '') . '");
               jQuery(\'#house_number_extension-' . $i . '\').val("' . (isset($_POST['house_number_extension-' . $i]) ? $_POST['house_number_extension-' . $i] : '') . '");
               jQuery(\'#postal_code-' . $i . '\').val("' . (isset($_POST['postal_code-' . $i]) ? $_POST['postal_code-' . $i] : '') . '");
               jQuery(\'#city-' . $i . '\').val("' . (isset($_POST['city-' . $i]) ? $_POST['city-' . $i] : '') . '");
               jQuery(\'#email-' . $i . '\').val("' . (isset($_POST['email-' . $i]) ? $_POST['email-' . $i] : '') . '");
               jQuery(\'#phonenumber-' . $i . '\').val("' . (isset($_POST['phonenumber-' . $i]) ? $_POST['phonenumber-' . $i] : '') . '");
               jQuery(\'#gender-' . $i . '\').val("' . (isset($_POST['gender-' . $i]) ? $_POST['gender-' . $i] : '') . '");
               jQuery(\'#employeeId-' . $i . '\').val("' . (isset($_POST['employeeId-' . $i]) ? $_POST['employeeId-' . $i] : '') . '");
               jQuery(\'#date_of_birth-' . $i . '\').val("' . (isset($_POST['date_of_birth-' . $i]) ? $_POST['date_of_birth-' . $i] : '') . '");
               jQuery(\'#costcentercode-' . $i . '\').val("' . (isset($_POST['costcentercode-' . $i]) ? $_POST['costcentercode-' . $i] : '') . '");
               jQuery(\'#company_position-' . $i . '\').val("' . (isset($_POST['company_position-' . $i]) ? $_POST['company_position-' . $i] : '') . '");
               jQuery(\'#iscontactperson-' . $i . '\').val("' . (isset($_POST['iscontactperson-' . $i]) ? $_POST['iscontactperson-' . $i] : '') . '");
               jQuery(\'#remark-' . $i . '\').val("' . (isset($_POST['remark-' . $i]) ? $_POST['remark-' . $i] : '') . '");
            ';
        }

        $jqueryshit = '
                    jQuery("#companychoiceyes").click(function () {
                        jQuery("#company_details").show("slow");
                        jQuery("#button_extra_student").show("slow");
                        jQuery(".contactdiv").show("slow");
                        jQuery("#bedrijfsemail").prop("required", "true");
                    });
                    jQuery("#companychoiceno").click(function () {
                        jQuery("#company_details").hide("slow");
                        jQuery("#button_extra_student").hide("slow");
                        jQuery(".contactdiv").hide();
                    });
            ';

        $bookingform .= '<script type="text/javascript">
                   function removeRow(e) {
                       e.target.parentElement.parentElement.remove();
                   }
                jQuery(document).ready(function () {
                    ' . $jqueryshit . '
                    ' . $studentForms . '
                    jQuery("#addRow").click(function() {
                        jQuery("<div/>", {"class": "extraPerson", html: GetHtml()}).hide().appendTo("#container").slideDown("slow");
                    });
                   function GetHtml() {
                       var len = jQuery(\'.extraPerson\').length;
                       console.log(len);
                       var $html = jQuery(\'.extraPersonTemplate\').clone();
                       document.getElementById("amount").value = len;
                       $html.find(\'[name=first_name]\')[0].id = "first_name-" + len;
                       jQuery("#first_name_"+len).attr("required", "true");
                       $html.find(\'[name=first_name]\')[0].name = "first_name-" + len;
                       $html.find(\'[name=last_name]\')[0].id = "last_name-" + len;
                       $html.find(\'[name=last_name]\')[0].name = "last_name-" + len;
                       $html.find(\'[name=initials]\')[0].id = "initials-" + len;
                       $html.find(\'[name=initials]\')[0].name = "initials-" + len;
                       $html.find(\'[name=prefix]\')[0].name = "prefix-" + len;
                       $html.find(\'[name=gender]\')[0].id = "gender-" + len;
                       $html.find(\'[name=gender]\')[0].name = "gender-" + len;
                       $html.find(\'[name=email]\')[0].id = "email-" + len;
                       $html.find(\'[name=email]\')[0].name = "email-" + len;
                       ';

        if ((isset($this->_options['vraagadrescursist']) && $this->_options['vraagadrescursist'] === '1')
            || (isset($data['has_stap']) && $data['has_stap'])) {
            $bookingform .= '$html.find(\'[name=address]\')[0].id = "address-" + len;';
            $bookingform .= '$html.find(\'[name=address]\')[0].name = "address-" + len;';
            $bookingform .= '$html.find(\'[name=house_number]\')[0].id = "house_number-" + len;';
            $bookingform .= '$html.find(\'[name=house_number]\')[0].name = "house_number-" + len;';
            $bookingform .= '$html.find(\'[name=postal_code]\')[0].id = "postal_code-" + len;';
            $bookingform .= '$html.find(\'[name=postal_code]\')[0].name = "postal_code-" + len;';
            $bookingform .= '$html.find(\'[name=city]\')[0].id = "city-" + len;';
            $bookingform .= '$html.find(\'[name=city]\')[0].name = "city-" + len;';
            if ((isset($this->_options['vraaghuisnrext']) && $this->_options['vraaghuisnrext'] === '1')) {
                $bookingform .= '$html.find(\'[name=house_number_extension]\')[0].id = "house_number_extension-" + len;';
                $bookingform .= '$html.find(\'[name=house_number_extension]\')[0].name = "house_number_extension-" + len;';
            }
        }

        if (isset($this->_options['vraagroepnaam'])
            && $this->_options['vraagroepnaam'] === '1') {
            $bookingform .= '$html.find(\'[name=nick_name]\')[0].id = "nick_name-" + len;';
            $bookingform .= '$html.find(\'[name=nick_name]\')[0].name = "nick_name-" + len;';
        }

        if (isset($this->_options['vraagmeisjesnaam'])
            && $this->_options['vraagmeisjesnaam'] === '1') {
            $bookingform .= '$html.find(\'[name=maiden_name]\')[0].id = "maiden_name-" + len;';
            $bookingform .= '$html.find(\'[name=maiden_name]\')[0].name = "maiden_name-" + len;';
        }

        if (isset($this->_options['vraag_internal_reference'])
            && $this->_options['vraag_internal_reference'] === '1') {
            $bookingform .= '$html.find(\'[name=internal_reference]\')[0].id = "internal_reference-" + len;';
            $bookingform .= '$html.find(\'[name=internal_reference]\')[0].name = "internal_reference-" + len;';
        }

        if (isset($this->_options['vraagpersoneelsnummer'])
            && $this->_options['vraagpersoneelsnummer'] === '1') {
            $bookingform .= '$html.find(\'[name=employeeId]\')[0].id = "employeeId-" + len;';
            $bookingform .= '$html.find(\'[name=employeeId]\')[0].name = "employeeId-" + len;';
        }

        if (isset($this->_options['vraagcostcentercode'])
            && $this->_options['vraagcostcentercode'] === '1') {
            $bookingform .= '$html.find(\'[name=costcentercode]\')[0].id = "costcentercode-" + len;';
            $bookingform .= '$html.find(\'[name=costcentercode]\')[0].name = "costcentercode-" + len;';
        }

        if (isset($this->_options['toonstudentremark'])
            && $this->_options['toonstudentremark'] === '1') {
            $bookingform .= '$html.find(\'[name=remark]\')[0].id = "remark-" + len;';
            $bookingform .= '$html.find(\'[name=remark]\')[0].name = "remark-" + len;';
        }

        if (isset($this->_options['toonformpositionstudent'])
            && $this->_options['toonformpositionstudent'] === '1') {
            $bookingform .= '$html.find(\'[name=company_position]\')[0].id = "company_position-" + len;';
            $bookingform .= '$html.find(\'[name=company_position]\')[0].name = "company_position-" + len;';
        }

        if ((isset($this->_options['toonformdateofbirth']) && $this->_options['toonformdateofbirth'] === '1')
            || (isset($data['has_stap']) && $data['has_stap'])) {
            $bookingform .= '$html.find(\'[name=date_of_birth]\')[0].id = "date_of_birth-" + len;';
            $bookingform .= '$html.find(\'[name=date_of_birth]\')[0].name = "date_of_birth-" + len;';
        }

        if (isset($this->_options['toonformcountryofbirth'])
            && $this->_options['toonformcountryofbirth'] === '1') {
            $bookingform .= '$html.find(\'[name=hometown]\')[0].id = "hometown-" + len;';
            $bookingform .= '$html.find(\'[name=hometown]\')[0].name = "hometown-" + len;';
        }

        if (isset($this->_options['toonformphonenumber'])
            && $this->_options['toonformphonenumber'] === '1') {
            $bookingform .= '$html.find(\'[name=phonenumber]\')[0].id = "phonenumber-" + len;';
            $bookingform .= '$html.find(\'[name=phonenumber]\')[0].name = "phonenumber-" + len;';
        }

        if (isset($this->_options['vraagcontactpersoon'])
            && $this->_options['vraagcontactpersoon'] === '1'
            && $this->_options['voorkeurbooleancompany'] === '1') {
            $bookingform .= '$html.find(\'[name=iscontactperson]\')[0].id = "iscontactperson-" + len;';
            $bookingform .= '$html.find(\'[name=iscontactperson]\')[0].name = "iscontactperson-" + len;';
            $bookingform .= '$html.find(\'[for=iscontactperson]\')[0].htmlFor = "iscontactperson-" + len;';
        }

        if (isset($this->_options['toonoptiecode95'], $data['has_code95'])
            && $this->_options['toonoptiecode95'] === '1'
            && $data['has_code95'] === true) {
            $bookingform .= '$html.find(\'[name=process_code95]\')[0].id = "process_code95-" + len;';
            $bookingform .= '$html.find(\'[name=process_code95]\')[0].name = "process_code95-" + len;';
            $bookingform .= '$html.find(\'[for=process_code95]\')[0].htmlFor = "process_code95-" + len;';
        }

        if (isset($this->_options['toonoptiesoob'], $data['has_soob'])
            && $this->_options['toonoptiesoob'] === '1'
            && $data['has_soob'] === true) {
            $bookingform .= '$html.find(\'[name=process_soob]\')[0].id = "process_soob-" + len;';
            $bookingform .= '$html.find(\'[name=process_soob]\')[0].name = "process_soob-" + len;';
            $bookingform .= '$html.find(\'[for=process_soob]\')[0].htmlFor = "process_soob-" + len;';
        }

        $bookingform .= ' return $html.html(); } }); </script>';

        echo $bookingform;
    }
}
