<?php
namespace Wa72\HtmlPageDom;

use Symfony\Component\CssSelector\CssSelector;
use Wa72\HtmlPrettymin\PrettyMin;

/**
 * This class represents a complete HTML document.
 *
 * It offers convenience functions for getting and setting elements of the document
 * such as setTitle(), getTitle(), setMeta($name, $value), getBody().
 *
 * It uses HtmlPageCrawler to navigate and manipulate the DOM tree.
 *
 * @author Christoph Singer
 * @license MIT
 */
class HtmlPage
{
    /**
     *
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var string
     */
    protected $url;

    /**
     *
     * @var HtmlPageCrawler
     */
    protected $crawler;

    public function __construct($content = '', $url = '', $charset = 'UTF-8')
    {
        $unsafeLibXml = \LIBXML_VERSION < 20900;
        $this->charset = $charset;
        $this->url = $url;
        if ($content == '') {
            $content = '<!DOCTYPE html><html><head><title></title></head><body></body></html>';
        }
        $current = libxml_use_internal_errors(true);
        if($unsafeLibXml) {
            $disableEntities = libxml_disable_entity_loader(true);
        }

        $this->dom = new \DOMDocument('1.0', $charset);
        $this->dom->validateOnParse = true;


        if (function_exists('mb_convert_encoding') && in_array(strtolower($charset), array_map('strtolower', mb_list_encodings()))) {
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', $charset);
        }

        @$this->dom->loadHTML($content);

        libxml_use_internal_errors($current);
        if($unsafeLibXml) {
            libxml_disable_entity_loader($disableEntities);
        }
        $this->crawler = new HtmlPageCrawler($this->dom);
    }

    /**
     * Get a HtmlPageCrawler object containing the root node of the HTML document
     *
     * @return HtmlPageCrawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * Get a DOMDocument object for the HTML document
     *
     * @return \DOMDocument
     */
    public function getDOMDocument()
    {
        return $this->dom;
    }

    /**
     * Sets the page title of the HTML document
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $t = $this->dom->getElementsByTagName('title')->item(0);
        if ($t == null) {
            $t = $this->dom->createElement('title');
            $this->getHeadNode()->appendChild($t);
        }
        $t->nodeValue = htmlspecialchars($title);
    }

    /**
     * Get the page title of the HTML document
     *
     * @return null|string
     */
    public function getTitle()
    {
        $t = $this->dom->getElementsByTagName('title')->item(0);
        if ($t == null) {
            return null;
        } else {
            return $t->nodeValue;
        }
    }

    /**
     * Set a META tag with specified 'name' and 'content' attributes
     *
     * @TODO: add support for multiple meta tags with the same name but different languages
     *
     * @param $name
     * @param $content
     */
    public function setMeta($name, $content)
    {
        $c = $this->filterXPath('descendant-or-self::meta[@name = \'' . $name . '\']');
        if (count($c) == 0) {
            $node = $this->dom->createElement('meta');
            $node->setAttribute('name', $name);
            $this->getHeadNode()->appendChild($node);
            $c->addNode($node);
        }
        $c->setAttribute('content', $content);
    }

    /**
     * Remove all meta tags with the specified name attribute
     *
     * @param string $name
     */
    public function removeMeta($name)
    {
        $meta = $this->filterXPath('descendant-or-self::meta[@name = \'' . $name . '\']');
        $meta->remove();
    }

    /**
     * Get the content attribute of a meta tag with the specified name attribute
     *
     * @param string $name
     * @return null|string
     */
    public function getMeta($name)
    {
        $node = $this->filterXPath('descendant-or-self::meta[@name = \'' . $name . '\']')->getNode(0);
        if ($node instanceof \DOMElement) {
            return $node->getAttribute('content');
        } else {
            return null;
        }
    }

    /**
     * Set the base tag with href attribute set to parameter $url
     *
     * @param string $url
     */
    public function setBaseHref($url)
    {
        $node = $this->filterXPath('descendant-or-self::base')->getNode(0);
        if ($node == null) {
            $node = $this->dom->createElement('base');
            $this->getHeadNode()->appendChild($node);
        }
        $node->setAttribute('href', $url);
    }

    /**
     * Get the href attribute from the base tag, null if not present in document
     *
     * @return null|string
     */
    public function getBaseHref()
    {
        $node = $this->filterXPath('descendant-or-self::base')->getNode(0);
        if ($node instanceof \DOMElement) {
            return $node->getAttribute('href');
        } else {
            return null;
        }
    }

    /**
     * Sets innerHTML content of an element specified by elementId
     *
     * @param string $elementId
     * @param string $html
     */
    public function setHtmlById($elementId, $html)
    {
        $this->getElementById($elementId)->setInnerHtml($html);
    }

    /**
     * Get the document's HEAD section as DOMElement
     *
     * @return \DOMElement
     */
    public function getHeadNode()
    {
        $head = $this->dom->getElementsByTagName('head')->item(0);
        if ($head == null) {
            $head = $this->dom->createElement('head');
            $head = $this->dom->documentElement->insertBefore($head, $this->getBodyNode());
        }
        return $head;
    }

    /**
     * Get the document's body as DOMElement
     *
     * @return \DOMElement
     */
    public function getBodyNode()
    {
        $body = $this->dom->getElementsByTagName('body')->item(0);
        if ($body == null) {
            $body = $this->dom->createElement('body');
            $body = $this->dom->documentElement->appendChild($body);
        }
        return $body;
    }

    /**
     * Get the document's HEAD section wrapped in a HtmlPageCrawler instance
     *
     * @return HtmlPageCrawler
     */
    public function getHead()
    {
        return new HtmlPageCrawler($this->getHeadNode());
    }

    /**
     * Get the document's body wrapped in a HtmlPageCrawler instance
     *
     * @return HtmlPageCrawler
     */
    public function getBody()
    {
        return new HtmlPageCrawler($this->getBodyNode());
    }

    public function __toString()
    {
        return $this->dom->saveHTML();
    }

    /**
     * Save this document to a HTML file or return HTML code as string
     *
     * @param string $filename If provided, output will be saved to this file, otherwise returned
     * @return string|void
     */
    public function save($filename = '')
    {
        if ($filename != '') {
            file_put_contents($filename, (string) $this);
            return;
        } else {
            return (string) $this;
        }
    }

    /**
     * Get an element in the document by it's id attribute
     *
     * @param string $id
     * @return HtmlPageCrawler
     */
    public function getElementById($id)
    {
        return $this->filterXPath('descendant-or-self::*[@id = \'' . $id . '\']');
    }

    /**
     * Filter nodes by using a CSS selector
     *
     * @param string $selector CSS selector
     * @return HtmlPageCrawler
     */
    public function filter($selector)
    {
        //echo "\n" . CssSelector::toXPath($selector) . "\n";
        return $this->crawler->filter($selector);
    }

    /**
     * Filter nodes by XPath expression
     *
     * @param string $xpath XPath expression
     * @return HtmlPageCrawler
     */
    public function filterXPath($xpath)
    {
        return $this->crawler->filterXPath($xpath);
    }

    /**
     * remove newlines from string and minimize whitespace (multiple whitespace characters replaced by one space)
     *
     * useful for cleaning up text retrieved by HtmlPageCrawler::text() (nodeValue of a DOMNode)
     *
     * @param string $string
     * @return string
     */
    public static function trimNewlines($string)
    {
        return Helpers::trimNewlines($string);
    }

    public function __clone()
    {
        $this->dom = $this->dom->cloneNode(true);
        $this->crawler = new HtmlPageCrawler($this->dom);
    }

    /**
     * minify the HTML document
     *
     * @param array $options Options passed to PrettyMin::__construct()
     * @return HtmlPage
     * @throws \Exception
     */
    public function minify(array $options = array())
    {
        if (!class_exists('Wa72\\HtmlPrettymin\\PrettyMin')) {
            throw new \Exception('Function minify needs composer package wa72/html-pretty-min');
        }
        $pm = new PrettyMin($options);
        $pm->load($this->dom)->minify();
        return $this;
    }

    /**
     * indent the HTML document
     *
     * @param array $options Options passed to PrettyMin::__construct()
     * @return HtmlPage
     * @throws \Exception
     */
    public function indent(array $options = array())
    {
        if (!class_exists('Wa72\\HtmlPrettymin\\PrettyMin')) {
            throw new \Exception('Function indent needs composer package wa72/html-pretty-min');
        }
        $pm = new PrettyMin($options);
        $pm->load($this->dom)->indent();
        return $this;
    }
}
