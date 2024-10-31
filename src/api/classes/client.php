<?php

class client
{
    private $_className;

    public function __construct()
    {
        $this->_className = 'planaday-api';
    }

    /**
     * @return client
     */
    public static function planaday_api_get_instance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }


    /**
     * Call the planaday API
     *
     * @param $url
     * @param $key
     * @param $call
     * @param $args
     * @param string $method
     * @return array|mixed|object|string
     */
    public function call($url, $key, $call, $args, $method = 'GET')
    {
        $options = get_option('planaday-api-general');
        $pluginVersionDatabase = get_option('planaday-api-version');
        $pluginVersion = (float)$pluginVersionDatabase * 100;
        $phpVersion = phpversion();

        $params = null;
        if (is_array($args) && !empty($args)) {
            foreach ($args as $name => $value) {
                if (!empty($value)) {
                    $params[] = sprintf('%s=%s', $name, $value);
                }
            }
        }

        $url = sprintf('%s/v1/%s', $url, $call);

        if (is_array($params)) {
            $url .= '?' . implode('&', $params);
            $url .= '&pluginversion=' . $pluginVersion;
        } else {
            $url .= '?pluginversion=' . $pluginVersion;
        }
        $url .= '&phpversion=' . $phpVersion;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            [
                sprintf('X-Api-Key: %s', $key),
                sprintf('X-Plugin-Version: %s', $pluginVersion),
                sprintf('X-PHP-Version: %s', $phpVersion),
                'Content-Type: application/json'
            ]
        );

        if (isset($options['toondebuginfo'])
            && $options['toondebuginfo'] === '1') {
            echo(sprintf(
                '<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>',
                __FUNCTION__, __FILE__, __LINE__,
                print_r($url, true))
            );
        }

        $response = curl_exec($ch);

        if (isset($options['toondebuginfo'])
            && $options['toondebuginfo'] === '1') {
            echo(sprintf(
                '<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>',
                __FUNCTION__, __FILE__, __LINE__,
                print_r($response, true))
            );
        }

        if (!$response) {
            $error = sprintf(
                'Error: "%s", Code: %s, HTTP-response: %s',
                curl_error($ch),
                curl_errno($ch),
                curl_getinfo($ch, CURLINFO_HTTP_CODE)
            );

            if (isset($options['toondebuginfo'])
                && $options['toondebuginfo'] === '1') {
                echo(sprintf(
                    '<PRE>Function: %s <br>File:%s<br>Line: %s<br><br>%s</PRE>',
                    __FUNCTION__, __FILE__, __LINE__,
                    print_r($error, true))
                );
            }

            curl_close($ch);
            return $error;
        }

        curl_close($ch);
        return json_decode($response, true);
    }
}
