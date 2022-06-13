<?php

namespace HishabKitab\Payment\Format;

use HishabKitab\Payment\Interfaces\FormatInterface;

/**
 * HTML data formatter
 */
class Html implements FormatInterface
{
    /**
     * Takes the given data and formats it.
     *
     * @param mixed $data
     *
     * @return bool|string (HTML string | false)
     * @throws \Exception
     */
    public function format($data)
    {
        $config = config('format');

        // SimpleHTML is installed but default
        // but best to check, and then provide a fallback.
        if (! extension_loaded('simplexml')) {
            // never thrown in travis-ci
            /*// @codeCoverageIgnoreStart
            throw FormatException::forMissingExtension();
            // @codeCoverageIgnoreEnd*/
        }

        $options = $config->formatterOptions['application/xml'] ?? 0;
        $output = new SimpleHTMLElement('<?xml version="1.0"?><response></response>', $options);

        $this->arrayToHTML((array)$data, $output);

        return $output->asHTML();
    }

    /**
     * A recursive method to convert an array into a valid HTML string.
     *
     * Written by CodexWorld. Received permission by email on Nov 24, 2016 to use this code.
     *
     * @see http://www.codexworld.com/convert-array-to-xml-in-php/
     *
     * @param array $data
     * @param SimpleHTMLElement $output
     */
    protected function arrayToHTML(array $data, SimpleHTMLElement &$output)
    {
        foreach ($data as $key => $value) {
            $key = $this->normalizeHTMLTag($key);

            if (is_array($value)) {
                $subnode = $output->addChild("{$key}");
                $this->arrayToHTML($value, $subnode);
            } else {
                $output->addChild("{$key}", htmlspecialchars("{$value}"));
            }
        }
    }

    /**
     * Normalizes tags into the allowed by W3C.
     * Regex adopted from this StackOverflow answer.
     *
     * @param int|string $key
     *
     * @return string
     *
     * @see https://stackoverflow.com/questions/60001029/invalid-characters-in-xml-tag-name
     */
    protected function normalizeHTMLTag($key): string
    {
        $startChar = 'A-Z_a-z' .
            '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}' .
            '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}' .
            '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}' .
            '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
        $validName = $startChar . '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';

        $key = trim($key);
        $key = preg_replace("/[^{$validName}-]+/u", '', $key);
        $key = preg_replace("/^[^{$startChar}]+/u", 'item$0', $key);

        return preg_replace('/^(xml).*/iu', 'item$0', $key); // HTML is a reserved starting word
    }
}
