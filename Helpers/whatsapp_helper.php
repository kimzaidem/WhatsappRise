<?php
    /**
     * [get_whatsapp_triggers Prepares an array of whatsapps triggers].
     *
     * @return [Array]
     */
    function whts_get_whatsapp_triggers(): array
    {
        return [
            '' => '',
            'leads'     => 'Lead <span class="text-muted"> (Triggers when new lead created)</span>',
            'client'    => 'Customer <span class="text-muted"> (Triggers when new contact created)</span>',
            'invoice'   => 'Invoice <span class="text-muted"> (Triggers when new invoice created)</span>',
            'tasks'     => 'Tasks <span class="text-muted"> (Triggers when new task created)</span>',
            'projects'  => 'Projects <span class="text-muted"> (Triggers when new project created)</span>',
            'proposals' => 'Proposals <span class="text-muted"> (Triggers when new proposals created)</span>',
            'ticket'    => 'Ticket <span class="text-muted"> (Triggers when new ticket created)</span>',
            'payment'   => 'Payments <span class="text-muted"> (Triggers when new payment created)</span>',
        ];
    }

    function whts_send_to_list(): array
    {
        return [
            '' => '',
            'staff'   => 'Staff <span class="text-muted"> (Will inform staff members)</span>',
            'contact' => 'Contact <span class="text-muted"> (Will inform primary contact of the customer)</span>'
        ];
    }

    function whts_get_template_list(): array
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('whatsapp_templates');
        $query = $builder->select('CONCAT(template_name," | ",language) as template,id')->orderBy('language')->get();

        $result = array(
            '' => '',
        );

        foreach ($query->getResult() as $value) {
            $result[$value->id] = $value->template;
        }

        return $result;
    }

    /**
     * [remove_blank_value remove blank values and return array].
     *
     * @param  [array] $var
     * @param  [string] $key_to_check
     *
     * @return [array]
     */
    if (!function_exists('remove_blank_value')) {
        function remove_blank_value($var, $key_to_check): array
        {
            $data = [];
            foreach ($var as $key => $value) {
                if ('' === $value[$key_to_check]) {
                    unset($var[$key]);
                    continue;
                }
                $data[] = $value;
            }

            return $data;
        }
    }

    function validate_request_url($request_url)
    {
        $sanitized_setting_value = get_sanitized_request_url($request_url);

        return filter_var($sanitized_setting_value, \FILTER_VALIDATE_URL);
    }

    function get_sanitized_request_url($request_url)
    {
        $sanitized_value = esc_url_with_merge_tags(encode_spaces_in_url($request_url));

        return is_url_without_scheme($request_url) ? "http://{$sanitized_value}" : $sanitized_value;
    }

    function esc_url_with_merge_tags($request_url)
    {
        return preg_replace('|[^a-z0-9-~+_.?#=!&;,/{:}%@$\|*\'()\[\]\\x80-\\xff]|i', '', $request_url);
    }

    function encode_spaces_in_url($request_url)
    {
        return str_replace(' ', '%20', wh_strip_all_tags($request_url, true));
    }

    function wh_strip_all_tags($string, $remove_breaks = false)
    {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);

        if ($remove_breaks) {
            $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        }

        return trim($string);
    }

    function is_url_without_scheme($request_url)
    {
        $parsed_url = wh_parse_url($request_url);

        if (rgar($parsed_url, 'host') && !rgar($parsed_url, 'scheme')) {
            return true;
        }

        $host = explode('/', rgar($parsed_url, 'path'));

        return
            false !== filter_var(gethostbyname($host[0]), \FILTER_VALIDATE_IP)
            && !rgar($parsed_url, 'scheme');
    }

    function rgar($array, $name)
    {
        if (isset($array[$name])) {
            return $array[$name];
        }

        return '';
    }

    function wh_parse_url($url, $component = -1)
    {
        $to_unset = [];
        $url      = (string) $url;

        if ('//' === substr($url, 0, 2)) {
            $to_unset[] = 'scheme';
            $url        = 'placeholder:'.$url;
        } elseif ('/' === substr($url, 0, 1)) {
            $to_unset[] = 'scheme';
            $to_unset[] = 'host';
            $url        = 'placeholder://placeholder'.$url;
        }

        $parts = parse_url($url);

        if (false === $parts) {
            // Parsing failure.
            return $parts;
        }

        // Remove the placeholder values.
        foreach ($to_unset as $key) {
            unset($parts[$key]);
        }

        return _get_component_from_parsed_url_array($parts, $component);
    }

    function _get_component_from_parsed_url_array($url_parts, $component = -1)
    {
        if (-1 === $component) {
            return $url_parts;
        }

        $key = _wh_translate_php_url_constant_to_key($component);
        if (false !== $key && is_array($url_parts) && isset($url_parts[$key])) {
            return $url_parts[$key];
        }

        return null;
    }

    function _wh_translate_php_url_constant_to_key($constant)
    {
        $translation = [
            \PHP_URL_SCHEME   => 'scheme',
            \PHP_URL_HOST     => 'host',
            \PHP_URL_PORT     => 'port',
            \PHP_URL_USER     => 'user',
            \PHP_URL_PASS     => 'pass',
            \PHP_URL_PATH     => 'path',
            \PHP_URL_QUERY    => 'query',
            \PHP_URL_FRAGMENT => 'fragment',
        ];

        if (isset($translation[$constant])) {
            return $translation[$constant];
        }

        return false;
    }

    if (!function_exists('isJson')) {
        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }
    }

    if (!function_exists('isXml')) {
        function isXml($string){
            $prev = libxml_use_internal_errors(true);

            $doc = simplexml_load_string($string);
            $errors = libxml_get_errors();

            libxml_clear_errors();
            libxml_use_internal_errors($prev);

            return (empty($errors)) ? true : false;
        }
    }

    /*End of file "whatsapps_helper.".php */
