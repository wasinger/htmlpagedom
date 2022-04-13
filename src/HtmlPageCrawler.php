<?php
namespace Wa72\HtmlPageDom;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Extends \Symfony\Component\DomCrawler\Crawler by adding tree manipulation functions
 * for HTML documents inspired by jQuery such as setInnerHtml(), css(), append(), prepend(), before(),
 * addClass(), removeClass()
 *
 * @author Christoph Singer
 * @license MIT
 *
 */
class HtmlPageCrawler extends Crawler
{
    /**
     * the (internal) root element name used when importing html fragments
     * */
    const FRAGMENT_ROOT_TAGNAME = '_root';

    /**
     * Get an HtmlPageCrawler object from a HTML string, DOMNode, DOMNodeList or HtmlPageCrawler
     *
     * This is the equivalent to jQuery's $() function when used for wrapping DOMNodes or creating DOMElements from HTML code.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList|array $content
     * @return HtmlPageCrawler
     * @api
     */
    public static function create($content)
    {
        if ($content instanceof HtmlPageCrawler) {
            return $content;
        } else {
            return new HtmlPageCrawler($content);
        }
    }

    /**
     * Adds the specified class(es) to each element in the set of matched elements.
     *
     * @param string $name One or more space-separated classes to be added to the class attribute of each matched element.
     * @return HtmlPageCrawler $this for chaining
     * @api
     */
    public function addClass($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $classes = preg_split('/\s+/s', $node->getAttribute('class'));
                $found = false;
                $count = count($classes);
                for ($i = 0; $i < $count; $i++) {
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
     * Insert content, specified by the parameter, after each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return HtmlPageCrawler $this for chaining
     * @api
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
                $newnode = static::importNewnode($newnode, $node, $i);
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
     * Insert HTML content as child nodes of each element after existing children
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment or DOMNode to append
     * @return HtmlPageCrawler $this for chaining
     * @api
     */
    public function append($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
                $node->appendChild($newnode);
                $newnodes[] = $newnode;
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Insert every element in the set of matched elements to the end of the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler A new Crawler object containing all elements appended to the target elements
     * @api
     */
    public function appendTo($element)
    {
        $e = self::create($element);
        $newnodes = array();
        foreach ($e as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($this as $newnode) {
                /** @var \DOMNode $newnode */
                if ($node !== $newnode) {
                    $newnode = static::importNewnode($newnode, $node, $i);
                    $node->appendChild($newnode);
                }
                $newnodes[] = $newnode;
            }
        }
        return self::create($newnodes);
    }

    /**
     * Sets an attribute on each element
     *
     * @param string $name
     * @param string $value
     * @return HtmlPageCrawler $this for chaining
     * @api
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
     * This is just an alias for attr() for naming consistency with setAttribute()
     *
     * @param string $name The attribute name
     * @return string|null The attribute value or null if the attribute does not exist
     * @throws \InvalidArgumentException When current node is empty
     */
    public function getAttribute($name)
    {
        return parent::attr($name);
    }

    /**
     * Insert content, specified by the parameter, before each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return HtmlPageCrawler $this for chaining
     * @api
     */
    public function before($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($content as $newnode) {
                /** @var \DOMNode $newnode */
                if ($node !== $newnode) {
                    $newnode = static::importNewnode($newnode, $node, $i);
                    $node->parentNode->insertBefore($newnode, $node);
                    $newnodes[] = $newnode;
                }
            }
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Create a deep copy of the set of matched elements.
     *
     * Equivalent to clone() in jQuery (clone is not a valid PHP function name)
     *
     * @return HtmlPageCrawler
     * @api
     */
    public function makeClone()
    {
        return clone $this;
    }

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
     * @api
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
        $styles = Helpers::cssStringToArray($this->getAttribute('style'));
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
                $styles = Helpers::cssStringToArray($node->getAttribute('style'));
                if ($value != '') {
                    $styles[$key] = $value;
                } elseif (isset($styles[$key])) {
                    unset($styles[$key]);
                }
                $node->setAttribute('style', Helpers::cssArrayToString($styles));
            }
        }
        return $this;
    }

    /**
     * Removes all child nodes and text from all nodes in set
     *
     * Equivalent to jQuery's empty() function which is not a valid function name in PHP
     * @return HtmlPageCrawler $this
     * @api
     */
    public function makeEmpty()
    {
        foreach ($this as $node) {
            $node->nodeValue = '';
        }
        return $this;
    }

    /**
     * Determine whether any of the matched elements are assigned the given class.
     *
     * @param string $name
     * @return bool
     * @api
     */
    public function hasClass($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement && $class = $node->getAttribute('class')) {
                $classes = preg_split('/\s+/s', $class);
                if (in_array($name, $classes)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Set the HTML contents of each element
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     * @api
     */
    public function setInnerHtml($content)
    {
        $content = self::create($content);
        foreach ($this as $node) {
            $node->nodeValue = '';
            foreach ($content as $newnode) {
                /** @var \DOMNode $node */
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node);
                $node->appendChild($newnode);
            }
        }
        return $this;
    }

    /**
     * Alias for Crawler::html() for naming consistency with setInnerHtml()
     *
     * @return string
     * @api
     */
    public function getInnerHtml()
    {
        return parent::html();
    }

    /**
     * Insert every element in the set of matched elements after the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler A new Crawler object containing all elements appended to the target elements
     * @api
     */
    public function insertAfter($element)
    {
        $e = self::create($element);
        $newnodes = array();
        foreach ($e as $i => $node) {
            /** @var \DOMNode $node */
            $refnode = $node->nextSibling;
            foreach ($this as $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
                if ($refnode === null) {
                    $node->parentNode->appendChild($newnode);
                } else {
                    $node->parentNode->insertBefore($newnode, $refnode);
                }
                $newnodes[] = $newnode;
            }
        }
        return self::create($newnodes);
    }

    /**
     * Insert every element in the set of matched elements before the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler A new Crawler object containing all elements appended to the target elements
     * @api
     */
    public function insertBefore($element)
    {
        $e = self::create($element);
        $newnodes = array();
        foreach ($e as $i => $node) {
            /** @var \DOMNode $node */
            foreach ($this as $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
                if ($newnode !== $node) {
                    $node->parentNode->insertBefore($newnode, $node);
                }
                $newnodes[] = $newnode;
            }
        }
        return self::create($newnodes);
    }

    /**
     * Insert content, specified by the parameter, to the beginning of each element in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content HTML code fragment
     * @return HtmlPageCrawler $this for chaining
     * @api
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
                $newnode = static::importNewnode($newnode, $node, $i);
                if ($refnode === null) {
                    $node->appendChild($newnode);
                } else if ($refnode !== $newnode) {
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
     * Insert every element in the set of matched elements to the beginning of the target.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler A new Crawler object containing all elements prepended to the target elements
     * @api
     */
    public function prependTo($element)
    {
        $e = self::create($element);
        $newnodes = array();
        foreach ($e as $i => $node) {
            $refnode = $node->firstChild;
            /** @var \DOMNode $node */
            foreach ($this as $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
                if ($newnode !== $node) {
                    if ($refnode === null) {
                        $node->appendChild($newnode);
                    } else {
                        $node->insertBefore($newnode, $refnode);
                    }
                }
                $newnodes[] = $newnode;
            }
        }
        return self::create($newnodes);
    }

    /**
     * Remove the set of matched elements from the DOM.
     *
     * (as opposed to Crawler::clear() which detaches the nodes only from Crawler
     * but leaves them in the DOM)
     *
     * @api
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
     * Remove an attribute from each element in the set of matched elements.
     *
     * Alias for removeAttribute for compatibility with jQuery
     *
     * @param string $name
     * @return HtmlPageCrawler
     * @api
     */
    public function removeAttr($name)
    {
        return $this->removeAttribute($name);
    }

    /**
     * Remove an attribute from each element in the set of matched elements.
     *
     * @param string $name
     * @return HtmlPageCrawler
     */
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
     * Remove a class from each element in the list
     *
     * @param string $name
     * @return HtmlPageCrawler $this for chaining
     * @api
     */
    public function removeClass($name)
    {
        foreach ($this as $node) {
            if ($node instanceof \DOMElement) {
                /** @var \DOMElement $node */
                $classes = preg_split('/\s+/s', $node->getAttribute('class'));
                $count = count($classes);
                for ($i = 0; $i < $count; $i++) {
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
     * Replace each target element with the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $element
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler A new Crawler object containing all elements appended to the target elements
     * @api
     */
    public function replaceAll($element)
    {
        $e = self::create($element);
        $newnodes = array();
        foreach ($e as $i => $node) {
            /** @var \DOMNode $node */
            $parent = $node->parentNode;
            $refnode  = $node->nextSibling;
            foreach ($this as $j => $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
                if ($j == 0) {
                    $parent->replaceChild($newnode, $node);
                } else {
                    $parent->insertBefore($newnode, $refnode);
                }
                $newnodes[] = $newnode;
            }
        }
        return self::create($newnodes);
    }

    /**
     * Replace each element in the set of matched elements with the provided new content and return the set of elements that was removed.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     * @api
     */
    public function replaceWith($content)
    {
        $content = self::create($content);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $parent = $node->parentNode;
            $refnode  = $node->nextSibling;
            foreach ($content as $j => $newnode) {
                /** @var \DOMNode $newnode */
                $newnode = static::importNewnode($newnode, $node, $i);
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
     * Get the combined text contents of each element in the set of matched elements, including their descendants.
     * This is what the jQuery text() function does, contrary to the Crawler::text() method that returns only
     * the text of the first node.
     *
     * @return string
     * @api
     */
    public function getCombinedText()
    {
        $text = '';
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            $text .= $node->nodeValue;
        }
        return $text;
    }

    /**
     * Set the text contents of the matched elements.
     *
     * @param string $text
     * @return HtmlPageCrawler
     * @api
     */
    public function setText($text)
    {
        $text = htmlspecialchars($text);
        foreach ($this as $node) {
            /** @var \DOMNode $node */
            $node->nodeValue = $text;
        }
        return $this;
    }

    /**
     * Add or remove one or more classes from each element in the set of matched elements, depending the class’s presence.
     *
     * @param string $classname One or more classnames separated by spaces
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     * @api
     */
    public function toggleClass($classname)
    {
        $classes = explode(' ', $classname);
        foreach ($this as $i => $node) {
            $c = self::create($node);
            /** @var \DOMNode $node */
            foreach ($classes as $class) {
                if ($c->hasClass($class)) {
                    $c->removeClass($class);
                } else {
                    $c->addClass($class);
                }
            }
        }
        return $this;
    }

    /**
     * Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place.
     *
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     * @api
     */
    public function unwrap()
    {
        $parents = array();
        foreach($this as $i => $node) {
            $parents[] = $node->parentNode;
        }

        self::create($parents)->unwrapInner();
        return $this;
    }

    /**
     * Remove the matched elements, but promote the children to take their place.
     *
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     * @api
     */
    public function unwrapInner()
    {
        foreach($this as $i => $node) {
            if (!$node->parentNode instanceof \DOMElement) {
                throw new \InvalidArgumentException('DOMElement does not have a parent DOMElement node.');
            }

            /** @var \DOMNode[] $children */
            $children = iterator_to_array($node->childNodes);
            foreach ($children as $child) {
                $node->parentNode->insertBefore($child, $node);
            }

            $node->parentNode->removeChild($node);
        }
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
     * @api
     */
    public function wrap($wrappingElement)
    {
        $content = self::create($wrappingElement);
        $newnodes = array();
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            $newnode = $content->getNode(0);
            /** @var \DOMNode $newnode */
//            $newnode = static::importNewnode($newnode, $node, $i);
            if ($newnode->ownerDocument !== $node->ownerDocument) {
                $newnode = $node->ownerDocument->importNode($newnode, true);
            } else {
                if ($i > 0) {
                    $newnode = $newnode->cloneNode(true);
                }
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
                if (!$elementFound) {
                    break;
                }
            }
            $newnode->appendChild($oldnode);
            $newnodes[] = $newnode;
        }
        $content->clear();
        $content->add($newnodes);
        return $this;
    }

    /**
     * Wrap an HTML structure around all elements in the set of matched elements.
     *
     * @param string|HtmlPageCrawler|\DOMNode|\DOMNodeList $content
     * @throws \LogicException
     * @return \Wa72\HtmlPageDom\HtmlPageCrawler $this for chaining
     * @api
     */
    public function wrapAll($content)
    {
        $content = self::create($content);
        $parent = $this->getNode(0)->parentNode;
        foreach ($this as $i => $node) {
            /** @var \DOMNode $node */
            if ($node->parentNode !== $parent) {
                throw new \LogicException('Nodes to be wrapped with wrapAll() must all have the same parent');
            }
        }

        $newnode = $content->getNode(0);
        /** @var \DOMNode $newnode */
        $newnode = static::importNewnode($newnode, $parent);

        $newnode = $parent->insertBefore($newnode,$this->getNode(0));
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
            if (!$elementFound) {
                break;
            }
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
     * @api
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
     * Get the HTML code fragment of all elements and their contents.
     *
     * If the first node contains a complete HTML document return only
     * the full code of this document.
     *
     * @return string HTML code (fragment)
     * @api
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
            return preg_replace('@^<'.self::FRAGMENT_ROOT_TAGNAME.'[^>]*>|</'.self::FRAGMENT_ROOT_TAGNAME.'>$@', '', $html);
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
     * Filters the list of nodes with a CSS selector.
     *
     * @param string $selector
     * @return HtmlPageCrawler
     */
    public function filter(string $selector): static
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
    public function filterXPath($xpath): static
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
        $d->preserveWhiteSpace = false;
        $root = $d->appendChild($d->createElement(self::FRAGMENT_ROOT_TAGNAME));
        $bodynode = Helpers::getBodyNodeFromHtmlFragment($content, $charset);
        foreach ($bodynode->childNodes as $child) {
            $inode = $root->appendChild($d->importNode($child, true));
            if ($inode) {
                $this->addNode($inode);
            }
        }
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

    /**
     * @param \DOMNode $newnode
     * @param \DOMNode $referencenode
     * @param int $clone
     * @return \DOMNode
     */
    protected static function importNewnode(\DOMNode $newnode, \DOMNode $referencenode, $clone = 0) {
        if ($newnode->ownerDocument !== $referencenode->ownerDocument) {
            $referencenode->ownerDocument->preserveWhiteSpace = false;
            $newnode = $referencenode->ownerDocument->importNode($newnode, true);
        } else {
            if ($clone > 0) {
                $newnode = $newnode->cloneNode(true);
            }
        }
        return $newnode;
    }

//    /**
//     * Checks whether the first node in the set is disconnected (has no parent node)
//     *
//     * @return bool
//     */
//    public function isDisconnected()
//    {
//        $parent = $this->getNode(0)->parentNode;
//        return ($parent == null || $parent->tagName == self::FRAGMENT_ROOT_TAGNAME);
//    }

    public function __get($name)
    {
        switch ($name) {
            case 'count':
            case 'length':
                return count($this);
        }
        throw new \Exception('No such property ' . $name);
    }
}
