<?php

class payment extends shortcodes
{
    public $_options;
    public $_paymentOptions;

    public function __construct()
    {
        $this->_className = 'planaday-api-payment';
        $this->_options = get_option('planaday-api-general');
        $this->_paymentOptions = get_option('planaday-api-payment');

        foreach ($this->_options as $key => $value) {
            if ($value === null || $value === '') {
                $this->_options[$key] = '0';
            }
        }
        foreach ($this->_paymentOptions as $key => $value) {
            if ($value === null || $value === '') {
                $this->_paymentOptions[$key] = '0';
            }
        }
    }

    /**
     * @return payment
     */
    public static function planaday_api_get_instance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    private function planaday_api_get_bookingid_from_sourcelink(string $sourceLink)
    {
        if (preg_match('/booking_id=[\w.]*/', $sourceLink, $matches)) {
            return str_replace('booking_id=', '', $matches[0]);
        }

        return null;
    }

    private function planaday_api_get_fielddata(array $fieldData)
    {
        $fieldList = [];

        foreach ($fieldData as $key => $value) {
            if (strstr($key, '_pt-field-')
                && !strstr($key, '-label')) {
                $label = $fieldData[$key . '-label'][0];
                if ((strstr($label, 'first_name')
                        || strstr($label, 'last_name')
                        || strstr($label, 'email')
                        || strstr($label, 'date_of_birth'))
                    && strpos($label, 'company') === false) {
                    $label .= '-0';
                }
                $fieldList[$label] = $value[0];
            } elseif (strstr($key, '-label')) {
                continue;
            } else {
                $fieldList[$key] = $value[0];
            }
        }

        return $fieldList;
    }


    public function verwerkbetaling($payment)
    {
        $paymentFields = $this->planaday_api_get_fielddata($payment->field_data);
        $paymentFields['id'] = $payment->id;
        $paymentFields['status'] = $payment->status;
        $bookingId = $this->planaday_api_get_bookingid_from_sourcelink($payment->field_data['_source_link'][0]);
        if ($payment->status === 'paid') {
            $payed = true;
        } else {
            $payed = false;
        }

        $paymentData = [
            'is_payed' => $payed,
            'payment' => [
                'party' => 'Mollie',
                'transaction_id' => $payment->transaction_id
            ]
        ];

        if ($payed === true) {
            $client = client::planaday_api_get_instance();
            $result = $client->call(
                $this->_options['url'],
                $this->_options['key'],
                sprintf('booking/payed/%s', $bookingId),
                json_encode($paymentData),
                'POST'
            );

            if ($result['result'] === 'OK') {
                $planadayPaid = 1;
            } else {
                $planadayPaid = 2;
            }

            if (isset($paymentFields['company_email'])
                && !empty($paymentFields['company_email'])) {
                $this->planaday_api_send_mail_to_company(true, true, $planadayPaid, $paymentFields);
            } else {
                $this->planaday_api_send_mail_to_student(true, $paymentFields);
            }
            $this->planaday_api_send_mail_to_tutor(true, true, $planadayPaid, $paymentFields);
            $this->planaday_api_redirect_or_text(true, true, $paymentFields);
        }

        if ($payed === false
            && (isset($this->_paymentOptions['cancelbookingafternopayment'])
            && $this->_paymentOptions['cancelbookingafternopayment'] === '1')) {
            $client = client::planaday_api_get_instance();
            $client->call(
                $this->_options['url'],
                $this->_options['key'],
                sprintf('booking/%s', $bookingId),
                json_encode($paymentData),
                'DELETE'
            );
            $this->planaday_api_redirect_or_text(false, false, $paymentFields);
        }
        $this->planaday_api_redirect_or_text(false, $payed, $paymentFields);
    }
}
