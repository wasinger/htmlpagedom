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

        $html = '<html><body>' . $html . '</body></html>';
        $d = self::loadHtml($html, $charset);
        return $d->getElementsByTagName('body')->item(0);
    }

    public static function loadHtml(string $html, $charset = 'UTF-8'): \DOMDocument
    {
        return self::parseXhtml($html, $charset);
    }
    /**
     * Function originally taken from Symfony\Component\DomCrawler\Crawler
     * (c) Fabien Potencier <fabien@symfony.com>
     * License: MIT
     */
    private static function parseXhtml(string $htmlContent, string $charset = 'UTF-8'): \DOMDocument
    {
        $htmlContent = self::convertToHtmlEntities($htmlContent, $charset);

        $internalErrors = libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', $charset);
        $dom->validateOnParse = true;

        if ('' !== trim($htmlContent)) {
            // PHP DOMDocument->loadHTML method tends to "eat" closing tags in html strings within script elements
            // Option LIBXML_SCHEMA_CREATE seems to prevent this
            // see https://stackoverflow.com/questions/24575136/domdocument-removes-html-tags-in-javascript-string
            @$dom->loadHTML($htmlContent, \LIBXML_SCHEMA_CREATE);
        }

        libxml_use_internal_errors($internalErrors);

        return $dom;
    }

    /**
     * Converts charset to HTML-entities to ensure valid parsing.
     * Function taken from Symfony\Component\DomCrawler\Crawler
     * (c) Fabien Potencier <fabien@symfony.com>
     * License: MIT
     */
    private static function convertToHtmlEntities(string $htmlContent, string $charset = 'UTF-8'): string
    {
        set_error_handler(function () { throw new \Exception(); });

        try {
            return mb_encode_numericentity($htmlContent, [0x80, 0x10FFFF, 0, 0x1FFFFF], $charset);
        } catch (\Exception|\ValueError) {
            try {
                $htmlContent = iconv($charset, 'UTF-8', $htmlContent);
                $htmlContent = mb_encode_numericentity($htmlContent, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
            } catch (\Exception|\ValueError) {
            }
            return $htmlContent;
        } finally {
            restore_error_handler();
        }
    }
}
