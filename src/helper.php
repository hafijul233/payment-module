<?php

use Laminas\Escaper\Escaper;

if (! function_exists('lang')) {
    function lang(string $key, ...$params)
    {
        //noting
    }
}

if (! function_exists('esc')) {
    /**
     * Performs simple auto-escaping of data for security reasons.
     * Might consider making this more complex at a later date.
     *
     * If $data is a string, then it simply escapes and returns it.
     * If $data is an array, then it loops over it, escaping each
     * 'value' of the key/value pairs.
     *
     * Valid context values: html, js, css, url, attr, raw
     *
     * @param array|string $data
     * @param string $context
     * @param string|null $encoding
     *
     * @return array|string
     */
    function esc($data, string $context = 'html', ?string $encoding = null)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                $value = esc($value, $context);
            }
        }

        if (is_string($data)) {
            $context = strtolower($context);

            // Provide a way to NOT escape data since
            // this could be called automatically by
            // the View library.
            if (empty($context) || $context === 'raw') {
                return $data;
            }

            if (! in_array($context, ['html', 'js', 'css', 'url', 'attr'], true)) {
                throw new InvalidArgumentException('Invalid escape context provided.');
            }

            $method = $context === 'attr' ? 'escapeHtmlAttr' : 'escape' . ucfirst($context);

            static $escaper;
            if (! $escaper) {
                $escaper = new Escaper($encoding);
            }

            if ($encoding && $escaper->getEncoding() !== $encoding) {
                $escaper = new Escaper($encoding);
            }

            $data = $escaper->{$method}($data);
        }

        return $data;
    }
}

if (! function_exists('config')) {
    /**
     * More simple way of getting config instances from Factories
     *
     * @return mixed
     */
    function config(string $location, $default = null)
    {
        $value = $default;
        $notations = explode(".", $location);
        $configFilePath = dirname(__DIR__) . "/config/{$notations[0]}.php";
        if (file_exists($configFilePath)) {
            $config = include $configFilePath;
            array_shift($notations);
            if (count($notations) > 0) {
                $temp = $config;
                foreach ($notations as $notation) {
                    if (isset($temp[$notation])) {
                        $temp = $temp[$notation];
                    } else {
                        $temp = null;
                    }
                }
                $value = $temp;
            } else {
                $value = $config;
            }
        } else {
            echo "File Does not Exist";
        }

        return $value;
    }
}
