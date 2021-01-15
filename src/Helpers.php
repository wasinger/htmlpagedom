<?php
namespace Wa72\HtmlPageDom;

/**
 * Static helper functions for HtmlPageDom
 *
 * @package Wa72\HtmlPageDom
 */
class Helpers {

    /**
     * remove newlines from string and minimize whitespace (multiple whitespace characters replaced by one space)
     * useful for cleaning up text retrieved by HtmlPageCrawler::text() (nodeValue of a DOMNode)
     *
     * @param string $string
     * @return string
     */
    public static function trimNewlines($string)
    {
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\r", ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        return trim($string);
    }

    /**
     * Convert CSS string to array
     *
     * @param string $css list of CSS properties separated by ;
     * @return array name=>value pairs of CSS properties
     */
    public static function cssStringToArray($css)
    {
        $statements = explode(';', preg_replace('/\s+/s', ' ', $css));
        $styles = array();
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ('' === $statement) {
                continue;
            }
            $p = strpos($statement, ':');
            if ($p <= 0) {
                continue;
            } // invalid statement, just ignore it
            $key = trim(substr($statement, 0, $p));
            $value = trim(substr($statement, $p + 1));
            $styles[$key] = $value;
        }
        return $styles;
    }

    /**
     * Convert CSS name->value array to string
     *
     * @param array $array name=>value pairs of CSS properties
     * @return string list of CSS properties separated by ;
     */
    public static function cssArrayToString($array)
    {
        $styles = '';
        foreach ($array as $key => $value) {
            $styles .= $key . ': ' . $value . ';';
        }
        return $styles;
    }

    /**
     * Helper function for getting a body element
     * from an HTML fragment
     *
     * @param string $html A fragment of HTML code
     * @param string $charset
     * @return \DOMNode The body node containing child nodes created from the HTML fragment
     */
    public static function getBodyNodeFromHtmlFragment($html, $charset = 'UTF-8')
    {
        $unsafeLibXml = \LIBXML_VERSION < 20900;
        $html = '<html><body>' . $html . '</body></html>';
        $current = libxml_use_internal_errors(true);
        if($unsafeLibXml) {
            $disableEntities = libxml_disable_entity_loader(true);
        }
        $d = new \DOMDocument('1.0', $charset);
        $d->validateOnParse = true;
        if (function_exists('mb_convert_encoding') && in_array(
                strtolower($charset),
                array_map('strtolower', mb_list_encodings())
            )
        ) {
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', $charset);
        }
        @$d->loadHTML($html);
        libxml_use_internal_errors($current);
        if($unsafeLibXml) {
            libxml_disable_entity_loader($disableEntities);
        }
        return $d->getElementsByTagName('body')->item(0);
    }
}
