<?php
namespace Wa72\HtmlPageDom;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Extends \Symfony\Component\DomCrawler\Crawler by adding tree manipulation functions
 * for HTML documents inspired by jQuery such as html(), css(), append(), prepend(), before(),
 * addClass(), removeClass()
 *
 * @author Christoph Singer
 * @license MIT
 *
 */
class HtmlPageCrawler extends Crawler
{

    /**
     * Get an HtmlPageCrawler object from a HTML string, DOMNode, DOMNodeList or HtmlPageCrawler
     *
     * This is the equivalent to jQuery's $() function when used for wrapping DOMNodes or creating DOMElements from HTML code.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return HtmlPageCrawler
     */
    static public function create($content)
    {
        if ($content instanceof HtmlPageCrawler) {
            return $content;
        } else {
            return new HtmlPageCrawler($content);
        }
    }

    /**
     * Get the innerHTML contents of the first element
     *
     * @return string HTML code fragment
     */
    public function getInnerHtml()
    {
        $node = $this->getNode(0);
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
     *
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

    public function __toString()
    {
        return $this->saveHTML();
    }

    /**
     * checks whether the first node contains a complete html document
     * (as opposed to a document fragment)
     *
     * @return boolean
     */
    public function isHtmlDocument()
    {
        $node = $this->getNode(0);
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
        $node = $this->getNode(0);
        $r = null;
        if ($node instanceof \DOMElement
            && $node->ownerDocument instanceof \DOMDocument
        ) {
            $r = $node->ownerDocument;
        }
        return $r;
    }

    /**
     * Delete all nodes in the list. Removes the nodes from DOM.
     *
     * (as opposed to Crawler::clear() which detaches the nodes only from Crawler
     * but leaves them in the DOM)
     */
    public function remove()
    {
        foreach ($this as $node) {
            /**
             * @var \DOMNode $node
             */
            if ($node->parentNode instanceof \DOMElement) {
                $node->parentNode->removeChild($node);
            }
        }
        $this->clear();
    }

    /**
     * Set the HTML contents of each element
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     */
    public function setInnerHtml($content)
    {
        $content = self::create($content);
        foreach ($this as $node) {
            $node->nodeValue = '';
            foreach ($content as $newnode) {
                /** @var \DOMNode $node */
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    $newnode = $newnode->cloneNode(true);
                }
                $node->appendChild($newnode);
            }
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
     * Alias for Crawler::attr() for equivalence with setAttribute()
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
                if ($node->hasAttribute($name)) {
                    $node->removeAttribute($name);
                }
            }
        }
        return $this;
    }

    /**
     * Insert HTML content as child nodes of each element after existing children
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment or DOMNode to append
     * @return HtmlPageCrawler $this for chaining
     */
    public function append($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    if ($i > 0) $newnode = $newnode->cloneNode(true);
                }
                $node->appendChild($newnode);
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }


    /**
     * Insert HTML content as child nodes of each element before existing children
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     */
    public function prepend($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            $refnode = $node->firstChild;
            /** @var \DOMNode $node */
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    if ($i > 0) $newnode = $newnode->cloneNode(true);
                }
                if ($refnode === null) {
                    $node->appendChild($newnode);
                } else {
                    $node->insertBefore($newnode, $refnode);
                }
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Insert content, specified by the parameter, before each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return HtmlPageCrawler $this for chaining
     */
    public function before($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    if ($i > 0) $newnode = $newnode->cloneNode(true);
                }
                $node->parentNode->insertBefore($newnode, $node);
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Insert content, specified by the parameter, after each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return HtmlPageCrawler $this for chaining
     */
    public function after($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $refnode = $node->nextSibling;
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    if ($i > 0) $newnode = $newnode->cloneNode(true);
                }
                if ($refnode === null) {
                    $node->parentNode->appendChild($newnode);
                } else {
                    $node->parentNode->insertBefore($newnode, $refnode);
                }
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Wrap an HTML structure around each element in the set of matched elements
     *
     * The HTML structure must contain only one root node, e.g.:
     * Works: <div><div></div></div>
     * Does not work: <div></div><div></div>
     *
     * @param string|HtmlPageCrawler|\DOMNode $wrappingElement
     * @return HtmlPageCrawler $this for chaining
     */
    public function wrap($wrappingElement)
    {
        $content = self::create($wrappingElement);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $newnode = $content->getFirstNode();
            /** @var \DOMNode $newnode */
            if ($newnode->ownerDocument !== $node->ownerDocument) {
                $newnode = $node->ownerDocument->importNode($newnode, true);
            } else {
                if ($i > 0) $newnode = $newnode->cloneNode(true);
            }
            $oldnode = $node->parentNode->replaceChild($newnode, $node);
            while ($newnode->hasChildNodes()) {
                $elementFound = false;
                foreach ($newnode->childNodes as $child) {
                    if ($child instanceof \DOMElement) {
                        $newnode = $child;
                        $elementFound = true;
                        break;
                    }
                }
                if (!$elementFound) break;
            }
            $newnode->appendChild($oldnode);
            $newnodes[] = $newnode;
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Get or set the HTML contents
     *
     * Function is here for compatibility with jQuery: When called with a parameter, it is
     * equivalent to setInnerHtml(), without parameter it is the same as getInnerHtml()
     *
     * @see HtmlPageCrawler::setInnerHtml()
     * @see HtmlPageCrawler::getInnerHtml()
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList|null $html The HTML content to set, or NULL to get the current content
     *
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
     *
     * Function is here for compatibility with jQuery; it is the same as getStyle() and setStyle()
     *
     * @see HtmlPageCrawler::getStyle()
     * @see HtmlPageCrawler::setStyle()
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
     *
     * Equivalent to jQuery's empty() function which is not a valid function name in PHP
     */
    public function makeEmpty()
    {
        foreach ($this as $node) {
            $node->nodeValue = '';
        }
    }

    /**
     * Filters the list of nodes with a CSS selector.
     *
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
        return parent::filterXPath($xpath);
    }

    /**
     * Adds HTML/XML content to the HtmlPageCrawler object (but not to the DOM of an already attached node).
     *
     * Function overriden from Crawler because HTML fragments are always added as complete documents there
     *
     *
     * @param string      $content A string to parse as HTML/XML
     * @param null|string $type    The content type of the string
     *
     * @return null|void
     */
    public function addContent($content, $type = null)
    {
        if (empty($type)) {
            $type = 'text/html;charset=UTF-8';
        }
        if (substr($type, 0, 9) == 'text/html' && !preg_match('/<html\b[^>]*>/i', $content)) {
            // string contains no <html> Tag => no complete document but an HTML fragment!
            $this->addHtmlFragment($content);
        } else {
            parent::addContent($content, $type);
        }
    }

    public function addHtmlFragment($content, $charset = 'UTF-8')
    {
        $d = new \DOMDocument('1.0', $charset);
        $root = $d->appendChild($d->createElement('_root'));
        $bodynode = self::getBodyNodeFromHtmlFragment($content, $charset);
        foreach ($bodynode->childNodes as $child) {
            $inode = $root->appendChild($d->importNode($child, true));
            if ($inode) $this->addNode($inode);
        }
    }

    /**
     * Helper function for getting a body element
     * from an HTML fragment
     *
     * @param string $html A fragment of HTML code
     * @param string $charset
     * @return \DOMNode The body node containing child nodes created from the HTML fragment
     */
    static function getBodyNodeFromHtmlFragment($html, $charset = 'UTF-8')
    {
        $html = '<html><body>' . $html . '</body></html>';
        $current = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
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
        libxml_disable_entity_loader($disableEntities);
        return $d->getElementsByTagName('body')->item(0);
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

    /**
     * returns the first node
     * deprecated, use getNode(0) instead
     *
     * @return \DOMNode|null
     * @deprecated
     * @see Crawler::getNode
     */
    public function getFirstNode()
    {
        return $this->getNode(0);
    }

    /**
     * Returns the node name of the first node of the list.
     *
     * in Crawler (parent), this function will be available starting with 2.6.0,
     * therefore this method be removed from here as soon as we don't need to keep compatibility
     * with Symfony < 2.6
     *
     * @return string The node name
     *
     * @throws \InvalidArgumentException When current node is empty
     */
    public function nodeName()
    {
        if (!count($this)) {
            throw new \InvalidArgumentException('The current node list is empty.');
        }
        return $this->getNode(0)->nodeName;
    }

    /**
     * Insert every element in the set of matched elements to the end of the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function appendTo($element)
    {
        $e = self::create($element);
        $e->append($this);
        return $this;
    }

    /**
     * Create a deep copy of the set of matched elements.
     *
     */
    public function __clone()
    {
        $newnodes = array();
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            $newnodes[] = $node->cloneNode(true);
        }
        $this->clear();
        $this->add($newnodes);
    }

    /**
     * Insert every element in the set of matched elements after the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function insertAfter($element)
    {
        $e = self::create($element);
        $e->after($this);
        return $this;
    }

    /**
     * Insert every element in the set of matched elements before the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function insertBefore($element)
    {
        $e = self::create($element);
        $e->before($this);
        return $this;
    }

    /**
     * Insert every element in the set of matched elements to the beginning of the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function prependTo($element)
    {
        $e = self::create($element);
        $e->prepend($this);
        return $this;
    }

    /**
     * Replace each target element with the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function replaceAll($element) {
        $e = self::create($element);
        $e->replaceWith($this);
        return $this;
    }

    /**
     * Replace each element in the set of matched elements with the provided new content and return the set of elements that was removed.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function replaceWith($content) {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $parent = $node->parentNode;
            $refnode  = $node->nextSibling;
            foreach ($content as $j => $newnode) {
                /** @var \DOMNode $newnode */
                if ($newnode->ownerDocument !== $node->ownerDocument) {
                    $newnode = $node->ownerDocument->importNode($newnode, true);
                } else {
                    if ($i > 0) $newnode = $newnode->cloneNode(true);
                }
                if ($j == 0) {
                    $parent->replaceChild($newnode, $node);
                } else {
                    $parent->insertBefore($newnode, $refnode);
                }
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Add or remove one or more classes from each element in the set of matched elements, depending on either the class’s presence or the value of the switch argument.
     *
     * @param string $classname One or more classnames separated by spaces
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function toggleClass($classname) {
        $classes = explode(' ', $classname);
        foreach ($this as $i => $node) {
            $c = self::create($node);
            /** @var \DOMNode $node */
               foreach ($classes as $class) {
                   if ($c->hasClass($class)) $c->removeClass($class);
                   else $c->addClass($class);
               }
        }
        return $this;
    }

    /**
     * Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place.
     *
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function unwrap()
    {
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            if ($parent = $node->parentNode) {
                $parent->parentNode->replaceChild($node, $parent);
            }
        }
        return $this;
    }

    /**
     * Wrap an HTML structure around all elements in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @throws \LogicException
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function wrapAll($content)
    {
        $content = self::create($content);
        $parent = $this->getNode(0)->parentNode;
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            if ($node->parentNode !== $parent) throw new \LogicException('Nodes to be wrapped with wrapAll() must all have the same parent');
        }

        $newnode = $content->getFirstNode();
        /** @var \DOMNode $newnode */
        if ($newnode->ownerDocument !== $parent->ownerDocument) {
            $newnode = $parent->ownerDocument->importNode($newnode, true);
        }

        $parent->appendChild($newnode);
        $content->clear();
        $content->add($newnode);

        while ($newnode->hasChildNodes()) {
            $elementFound = false;
            foreach ($newnode->childNodes as $child) {
                if ($child instanceof \DOMElement) {
                    $newnode = $child;
                    $elementFound = true;
                    break;
                }
            }
            if (!$elementFound) break;
        }
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $newnode->appendChild($node);
        }
        return $this;
    }

    /**
     * Wrap an HTML structure around the content of each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     */
    public function wrapInner($content)
    {
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            self::create($node->childNodes)->wrapAll($content);
        }
        return $this;
    }

    /**
     * Adds a node to the current list of nodes.
     *
     * This method uses the appropriate specialized add*() method based
     * on the type of the argument.
     *
     * Overwritten from parent to allow Crawler to be added
     *
     * @param null|\DOMNodeList|array|\DOMNode|Crawler $node A node
     *
     * @api
     */
    public function add($node)
    {
        if ($node instanceof Crawler) {
            foreach ($node as $childnode) {
                $this->addNode($childnode);
            }
        } else {
            parent::add($node);
        }
    }
}