<?php
namespace Wa72\HtmlPageDom;

use Symfony\Component\CssSelector\CssSelector;

/**
 * This class represents a complete HTML document.
 * It offers convenience functions for getting and setting elements of the document
 * such as setTitle(), getTitle(), setMeta($name, $value), getBody().
 *
 * Internally it uses HtmlPageCrawler to navigate and manipulate the
 * DOM tree.
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
        $this->charset = $charset;
        $this->url = $url;
        if ($content == '') $content = '<!DOCTYPE html><html><head><title></title></head><body></body></html>';
        $current = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);

        $this->dom = new \DOMDocument('1.0', $charset);
        $this->dom->validateOnParse = true;


        if (function_exists('mb_convert_encoding') && in_array(strtolower($charset), array_map('strtolower', mb_list_encodings()))) {
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', $charset);
        }

        @$this->dom->loadHTML($content);
        $this->dom->formatOutput = true;

        libxml_use_internal_errors($current);
        libxml_disable_entity_loader($disableEntities);
        $this->crawler = new HtmlPageCrawler($this->dom);
    }

    /**
     * @return HtmlPageCrawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @return \DOMDocument
     */
    public function getDOMDocument()
    {
        return $this->dom;
    }

    public function setTitle($title)
    {
        $t = $this->dom->getElementsByTagName('title')->item(0);
        if ($t == null) {
            $t = $this->dom->createElement('title');
            $this->getHeadNode()->appendChild($t);
        }
        $t->nodeValue = htmlspecialchars($title);
    }

    public function getTitle()
    {
        $t = $this->dom->getElementsByTagName('title')->item(0);
        if ($t == null) return null;
        else return $t->nodeValue;
    }

    /**
     * Set a meta tag
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

    public function removeMeta($name)
    {
        $meta = $this->filterXPath('descendant-or-self::meta[@name = \'' . $name . '\']');
        $meta->delete();
    }

    public function getMeta($name)
    {
        $node = $this->filterXPath('descendant-or-self::meta[@name = \'' . $name . '\']')->getFirstNode();
        if ($node instanceof \DOMElement) {
            return $node->getAttribute('content');
        } else {
            return null;
        }
    }

    public function setBaseHref($url)
    {
        $node = $this->filterXPath('descendant-or-self::base')->getFirstNode();
        if ($node == null) {
            $node = $this->dom->createElement('base');
            $this->getHeadNode()->appendChild($node);
        }
        $node->setAttribute('href', $url);
    }

    public function getBaseHref()
    {
        $node = $this->filterXPath('descendant-or-self::base')->getFirstNode();
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
     *
     * @return \DOMNode
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
     *
     * @return \DOMNode
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
     * @return HtmlPageCrawler
     */
    public function getHead()
    {
        return new HtmlPageCrawler($this->getHeadNode());
    }

    /**
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
     *
     * @param string $filename If provided, output will be saved to this file, otherwise returned
     * @return string|void
     */
    public function save($filename = null)
    {
        if ($filename != null) {
            file_put_contents($filename, $this->__toString());
            return;
        } else {
            return $this->__toString();
        }
    }

    /**
     *
     * @param string $id
     * @return HtmlPageCrawler
     */
    public function getElementById($id)
    {
        return $this->filterXPath('descendant-or-self::*[@id = \'' . $id . '\']');
    }

    /**
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
     * useful for cleaning up text retrieved by HtmlPageCrawler::text() (nodeValue of a DOMNode)
     *
     * @param string $string
     * @return string
     */
    public static function trimNewlines($string)
    {
        return HtmlPageCrawler::trimNewlines($string);
    }

    public function __clone()
    {
        $this->dom = $this->dom->cloneNode(true);
        $this->dom->formatOutput = true;
        $this->crawler = new HtmlPageCrawler($this->dom);
    }
}
