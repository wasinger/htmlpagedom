<?php
namespace Wa72\HtmlPageDom;

use Symfony\Component\DomCrawler\Crawler;


class HtmlPageCrawler extends Crawler
{
    /**
     * returns the first node
     *
     * @return \DOMNode|null
     */
    public function getFirstNode()
    {
        $this->rewind();
        if ($this->valid()) {
            return $this->current();
        } else {
            return null;
        }
    }

    /**
     * returns the node name of the first node
     *
     * @return string|null
     */
    public function nodeName()
    {
        $node = $this->getFirstNode();
        if ($node instanceof \DOMNode) {
            return $node->nodeName;
        } else {
            return null;
        }
    }

    /**
     * Get the innerHTML contents of the first element
     *
     * @return string HTML code fragment
     */
    public function getInnerHtml()
    {
        $node = $this->getFirstNode();
        if ($node instanceof \DOMNode) {
            $doc = new \DOMDocument('1.0', 'UTF-8');
            $doc->appendChild($doc->importNode($node, true));
            $html = trim($doc->saveHTML());
            $tag = $node->nodeName;
            return preg_replace('@^<' . $tag . '[^>]*>|</' . $tag . '>$@', '', $html);
        } else {
            return '';
        }
    }

    /**
     * Get the HTML code fragment of all elements and their contents.
     * If the first node contains a complete HTML document return only
     * the full code of this document.
     *
     * @return string HTML code (fragment)
     */
    public function saveHTML()
    {
        if ($this->isHtmlDocument()) {
            return $this->getDOMDocument()->saveHTML();
        } else {
            $doc = new \DOMDocument('1.0', 'UTF-8');
            $root = $doc->appendChild($doc->createElement('_root'));
            foreach ($this as $node) {
                $root->appendChild($doc->importNode($node, true));
            }
            $html = trim($doc->saveHTML());
            return preg_replace('@^<_root[^>]*>|</_root>$@', '', $html);
        }
    }

    /**
     * checks whether the first node contains a complete html document
     * (as opposed to a document fragment)
     *
     * @return boolean
     */
    public function isHtmlDocument()
    {
        $node = $this->getFirstNode();
        if ($node instanceof \DOMElement
            && $node->ownerDocument instanceof \DOMDocument
            && $node->ownerDocument->documentElement === $node
            && $node->nodeName == 'html'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get ownerDocument of the first element
     *
     * @return \DOMDocument|null
     */
    public function getDOMDocument()
    {
        $node = $this->getFirstNode();
        $r = null;
        if ($node instanceof \DOMElement
            && $node->ownerDocument instanceof \DOMDocument
        ) {
            $r = $node->ownerDocument;
        }
        return $r;
    }

    /**
     * delete all nodes in the list
     */
    public function delete()
    {
        foreach ($this as $node) {
            if ($node->parentNode instanceof \DOMNode) {
                $node->parentNode->removeChild($node);
            }
            $this->detach($node);
        }
    }

    /**
     * Set the HTML contents of each element
     *
     * @param string $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     */
    public function setInnerHtml($content)
    {
        $xml = $this->getXMLFromHtmlFragment($content);
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            $node->nodeValue = '';
            if ($xml != '') $node->appendChild($this->getDOMDocumentFragment($node, $xml));
        }
        return $this;
    }

    /**
     * Sets an attribute on each element
     *
     * @param string $name
     * @param string $value
     * @return HtmlPageCrawler $this for chaining
     */
    public function setAttribute($name, $value)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $node->setAttribute($name, $value);
            }
        }
        return $this;
    }

    /**
     * Returns the attribute value of the first node of the list.
     * Alias for attr() for equivalence with setAttribute()
     *
     * @param string $name The attribute name
     * @return string The attribute value
     * @throws \InvalidArgumentException When current node is empty
     *
     */
    public function getAttribute($name)
    {
        return $this->attr($name);
    }

    public function removeAttribute($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                if ($node->hasAttribute($name)) $node->removeAttribute($name);
            }
        }
        return $this;
    }

    /**
     * Insert HTML content as child nodes of each element after existing children
     *
     * @param string $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     */
    public function append($content)
    {
        $xml = $this->getXMLFromHtmlFragment($content);
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            $node->appendChild($this->getDOMDocumentFragment($node, $xml));
        }
        return $this;
    }

    /**
     * Insert HTML content as child nodes of each element before existing children
     *
     * @param string $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     */
    public function prepend($content)
    {
        $xml = $this->getXMLFromHtmlFragment($content);
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            if (null === $node->firstChild) {
                $node->appendChild($this->getDOMDocumentFragment($node, $xml));
            } else {
                $node->insertBefore($this->getDOMDocumentFragment($node, $xml), $node->firstChild);
            }
        }
        return $this;
    }

    /**
     * Insert content, specified by the parameter, before each element in the set of matched elements.
     *
     * @param string $content
     * @return HtmlPageCrawler $this for chaining
     */
    public function before($content)
    {
        $xml = $this->getXMLFromHtmlFragment($content);
        foreach ($this as $node) {
            /** @var \DOMElement $node */
            $node->parentNode->insertBefore($this->getDOMDocumentFragment($node, $xml), $node);
        }
        return $this;
    }

    /**
     * Insert content, specified by the parameter, after each element in the set of matched elements.
     *
     * @param string $content
     * @return HtmlPageCrawler $this for chaining
     */
    public function after($content)
    {
        /* TODO: not yet implemented */
        throw new \Exception('method after() not yet implemented');
        return $this;
    }

    /**
     * Wrap an HTML structure around each element in the set of matched elements
     *
     * @param string $wrappingElement
     * @return HtmlPageCrawler $this for chaining
     */
    public function wrap($wrappingElement)
    {
        /* TODO: not yet implemented */
        throw new \Exception('method wrap() not yet implemented');
        $xml = $this->getXMLFromHtmlFragment($wrappingElement);

        return $this;
    }

    /**
     * Get or set the HTML contents
     * Function is here for compatibility with jQuery
     *
     * @param string|null $html The HTML content to set, or NULL to get the current content
     * @return HtmlPageCrawler|string If no param is provided, returns the HTML content of the first element
     */
    public function html($html = null)
    {
        if (null === $html) {
            return $this->getInnerHtml();
        } else {
            $this->setInnerHtml($html);
            return $this;
        }
    }

    /**
     * Get one CSS style property of the first element or set it for all elements in the list
     * Function is here for compatibility with jQuery
     *
     * @param string $key The name of the style property
     * @param null|string $value The CSS value to set, or NULL to get the current value
     * @return HtmlPageCrawler|string If no param is provided, returns the CSS styles of the first element
     */
    public function css($key, $value = null)
    {
        if (null === $value) {
            return $this->getStyle($key);
        } else {
            return $this->setStyle($key, $value);
        }
    }

    /**
     * get one CSS style property of the first element
     *
     * @param string $key name of the property
     * @return string|null value of the property
     */
    public function getStyle($key)
    {
        $styles = $this->cssStringToArray($this->getAttribute('style'));
        return (isset($styles[$key]) ? $styles[$key] : null);
    }

    /**
     * set one CSS style property for all elements in the list
     *
     * @param string $key name of the property
     * @param string $value value of the property
     * @return HtmlPageCrawler $this for chaining
     */
    public function setStyle($key, $value)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $styles = $this->cssStringToArray($node->getAttribute('style'));
                if ($value != '') {
                    $styles[$key] = $value;
                } elseif (isset($styles[$key])) {
                    unset($styles[$key]);
                }
                $node->setAttribute('style', $this->cssArrayToString($styles));
            }
        }
        return $this;
    }

    /**
     * Add a class to all elements in the list
     *
     * @param string $name
     * @return HtmlPageCrawler $this for chaining
     */
    public function addClass($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $classes = preg_split('/\s+/s', $node->getAttribute('class'));
                $found = false;
                for ($i = 0; $i < count($classes); $i++) {
                    if ($classes[$i] == $name) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $classes[] = $name;
                    $node->setAttribute('class', trim(join(' ', $classes)));
                }
            }
        }
        return $this;
    }

    /**
     * Check whether the first element has a certain class
     *
     * @param string $name
     * @return bool
     */
    public function hasClass($name)
    {
        $class = $this->getAttribute('class');
        $classes = preg_split('/\s+/s', $class);
        return in_array($name, $classes);
    }

    /**
     * Remove a class from all elements in the list
     *
     * @param string $name
     * @return HtmlPageCrawler $this for chaining
     */
    public function removeClass($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $classes = preg_split('/\s+/s', $node->getAttribute('class'));
                for ($i = 0; $i < count($classes); $i++) {
                    if ($classes[$i] == $name) {
                        unset($classes[$i]);
                    }
                }
                $node->setAttribute('class', trim(join(' ', $classes)));
            }
        }
        return $this;
    }

    /**
     * Convert CSS string to array
     *
     * @param string $css list of CSS properties separated by ;
     * @return array name=>value pairs of CSS properties
     */
    protected function cssStringToArray($css)
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
    protected function cssArrayToString($array)
    {
        $styles = '';
        foreach ($array as $key => $value) {
            $styles .= $key . ': ' . $value . ';';
        }
        return $styles;
    }

    /**
     * Removes all child nodes and text from all nodes in set
     */
    public function makeEmpty()
    {
        foreach ($this as $node) {
            $node->nodeValue = '';
        }
    }

    /**
     * @param string $selector
     * @return HtmlPageCrawler
     */
    public function filter($selector)
    {
        return parent::filter($selector);
    }

    /**
     * Filters the list of nodes with an XPath expression.
     *
     * @param string $xpath An XPath expression
     *
     * @return HtmlPageCrawler A new instance of Crawler with the filtered list of nodes
     *
     * @api
     */
    public function filterXPath($xpath)
    {
        $result = array();
        foreach ($this as $node) {
            $domxpath = new \DOMXPath($node->ownerDocument);
            $nodes = $domxpath->query($xpath, $node);
            foreach ($nodes as $newnode) {
                $result[] = $newnode;
            }
        }
        return new static($result);
    }

    /**
     * Get a XML representation from a HTML code fragment for use with DOMDocumentFragment
     *
     * @param string $html Fragment of html code (MUST NOT contain html and body tags!)
     * @param string $charset
     * @return string XML code fragment for use with DOMDocumentFragment::loadXML
     */
    protected function getXMLFromHtmlFragment($html, $charset = 'UTF-8')
    {
        $html = '<html><body>' . $html . '</body></html>';
        $current = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
        $d = new \DOMDocument('1.0', $charset);
        if (function_exists('mb_convert_encoding') && in_array(
            strtolower($charset),
            array_map('strtolower', mb_list_encodings())
        )
        ) {
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', $charset);
        }
        @$d->loadHTML($html);
        libxml_use_internal_errors($current);
        libxml_disable_entity_loader($disableEntities);
        $xml = $d->saveXML($d->getElementsByTagName('body')->item(0));
        return preg_replace('@^<body[^>]*>|</body>$@', '', $xml);
    }

    /**
     *
     * @param \DOMNode $node
     * @param string $xml
     * @return \DOMDocumentFragment
     */
    protected function getDOMDocumentFragment($node, $xml)
    {
        $frag = $node->ownerDocument->createDocumentFragment();
        $frag->appendXML($xml);
        return $frag;
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
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\r", ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        return trim($string);
    }
}