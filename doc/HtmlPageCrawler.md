# Wa72\HtmlPageDom\HtmlPageCrawler  

Extends \Symfony\Component\DomCrawler\Crawler by adding tree manipulation functions
for HTML documents inspired by jQuery such as setInnerHtml(), css(), append(), prepend(), before(),
addClass(), removeClass()

## Implements:
Countable, IteratorAggregate, Traversable, Stringable

## Extend:

Symfony\Component\DomCrawler\Crawler

## Methods

| Name | Description |
|------|-------------|
|[__clone](#htmlpagecrawler__clone)||
|[__get](#htmlpagecrawler__get)||
|[__toString](#htmlpagecrawler__tostring)||
|[addClass](#htmlpagecrawleraddclass)|Adds the specified class(es) to each element in the set of matched elements.|
|[addHtmlFragment](#htmlpagecrawleraddhtmlfragment)||
|[after](#htmlpagecrawlerafter)|Insert content, specified by the parameter, after each element in the set of matched elements.|
|[append](#htmlpagecrawlerappend)|Insert HTML content as child nodes of each element after existing children|
|[appendTo](#htmlpagecrawlerappendto)|Insert every element in the set of matched elements to the end of the target.|
|[before](#htmlpagecrawlerbefore)|Insert content, specified by the parameter, before each element in the set of matched elements.|
|[create](#htmlpagecrawlercreate)|Get an HtmlPageCrawler object from a HTML string, DOMNode, DOMNodeList or HtmlPageCrawler|
|[css](#htmlpagecrawlercss)|Get one CSS style property of the first element or set it for all elements in the list|
|[getAttribute](#htmlpagecrawlergetattribute)|Returns the attribute value of the first node of the list.|
|[getCombinedText](#htmlpagecrawlergetcombinedtext)|Get the combined text contents of each element in the set of matched elements, including their descendants.|
|[getDOMDocument](#htmlpagecrawlergetdomdocument)|get ownerDocument of the first element|
|[getInnerHtml](#htmlpagecrawlergetinnerhtml)|Alias for Crawler::html() for naming consistency with setInnerHtml()|
|[getStyle](#htmlpagecrawlergetstyle)|get one CSS style property of the first element|
|[hasClass](#htmlpagecrawlerhasclass)|Determine whether any of the matched elements are assigned the given class.|
|[insertAfter](#htmlpagecrawlerinsertafter)|Insert every element in the set of matched elements after the target.|
|[insertBefore](#htmlpagecrawlerinsertbefore)|Insert every element in the set of matched elements before the target.|
|[isHtmlDocument](#htmlpagecrawlerishtmldocument)|checks whether the first node contains a complete html document (as opposed to a document fragment)|
|[makeClone](#htmlpagecrawlermakeclone)|Create a deep copy of the set of matched elements.|
|[makeEmpty](#htmlpagecrawlermakeempty)|Removes all child nodes and text from all nodes in set|
|[prepend](#htmlpagecrawlerprepend)|Insert content, specified by the parameter, to the beginning of each element in the set of matched elements.|
|[prependTo](#htmlpagecrawlerprependto)|Insert every element in the set of matched elements to the beginning of the target.|
|[remove](#htmlpagecrawlerremove)|Remove the set of matched elements from the DOM.|
|[removeAttr](#htmlpagecrawlerremoveattr)|Remove an attribute from each element in the set of matched elements.|
|[removeAttribute](#htmlpagecrawlerremoveattribute)|Remove an attribute from each element in the set of matched elements.|
|[removeClass](#htmlpagecrawlerremoveclass)|Remove a class from each element in the list|
|[replaceAll](#htmlpagecrawlerreplaceall)|Replace each target element with the set of matched elements.|
|[replaceWith](#htmlpagecrawlerreplacewith)|Replace each element in the set of matched elements with the provided new content and return the set of elements that was removed.|
|[saveHTML](#htmlpagecrawlersavehtml)|Get the HTML code fragment of all elements and their contents.|
|[setAttribute](#htmlpagecrawlersetattribute)|Sets an attribute on each element|
|[setInnerHtml](#htmlpagecrawlersetinnerhtml)|Set the HTML contents of each element|
|[setStyle](#htmlpagecrawlersetstyle)|set one CSS style property for all elements in the list|
|[setText](#htmlpagecrawlersettext)|Set the text contents of the matched elements.|
|[toggleClass](#htmlpagecrawlertoggleclass)|Add or remove one or more classes from each element in the set of matched elements, depending the class’s presence.|
|[unwrap](#htmlpagecrawlerunwrap)|Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place.|
|[unwrapInner](#htmlpagecrawlerunwrapinner)|Remove the matched elements, but promote the children to take their place.|
|[wrap](#htmlpagecrawlerwrap)|Wrap an HTML structure around each element in the set of matched elements|
|[wrapAll](#htmlpagecrawlerwrapall)|Wrap an HTML structure around all elements in the set of matched elements.|
|[wrapInner](#htmlpagecrawlerwrapinner)|Wrap an HTML structure around the content of each element in the set of matched elements.|

## Inherited methods

| Name | Description |
|------|-------------|
| [__construct](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.__construct.php) | - |
|add|Adds a node to the current list of nodes.|
|addContent|Adds HTML/XML content.|
|addDocument|Adds a \DOMDocument to the list of nodes.|
|addHtmlContent|Adds an HTML content to the list of nodes.|
|addNode|Adds a \DOMNode instance to the list of nodes.|
|addNodeList|Adds a \DOMNodeList to the list of nodes.|
|addNodes|Adds an array of \DOMNode instances to the list of nodes.|
|addXmlContent|Adds an XML content to the list of nodes.|
|ancestors|Returns the ancestors of the current selection.|
|attr|Returns the attribute value of the first node of the list.|
|children|Returns the children nodes of the current selection.|
|clear|Removes all the nodes.|
|closest|Return first parents (heading toward the document root) of the Element that matches the provided selector.|
| [count](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.count.php) | - |
|each|Calls an anonymous function on each node of the list.|
|eq|Returns a node given its position in the node list.|
|evaluate|Evaluates an XPath expression.|
|extract|Extracts information from the list of nodes.|
|filter|Filters the list of nodes with a CSS selector.|
|filterXPath|Filters the list of nodes with an XPath expression.|
|first|Returns the first node of the current selection.|
|form|Returns a Form object for the first node in the list.|
|getBaseHref|Returns base href.|
| [getIterator](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.getiterator.php) | - |
| [getNode](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.getnode.php) | - |
|getUri|Returns the current URI.|
|html|Returns the first node of the list as HTML.|
|image|Returns an Image object for the first node in the list.|
|images|Returns an array of Image objects for the nodes in the list.|
|innerText|Returns only the inner text that is the direct descendent of the current node, excluding any child nodes.|
|last|Returns the last node of the current selection.|
|link|Returns a Link object for the first node in the list.|
|links|Returns an array of Link objects for the nodes in the list.|
| [matches](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.matches.php) | - |
|nextAll|Returns the next siblings nodes of the current selection.|
|nodeName|Returns the node name of the first node of the list.|
| [outerHtml](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.outerhtml.php) | - |
|previousAll|Returns the previous sibling nodes of the current selection.|
|reduce|Reduces the list of nodes by calling an anonymous function.|
| [registerNamespace](https://secure.php.net/manual/en/symfony\component\domcrawler\crawler.registernamespace.php) | - |
|selectButton|Selects a button by name or alt value for images.|
|selectImage|Selects images by alt value.|
|selectLink|Selects links by name or alt value for clickable images.|
|setDefaultNamespacePrefix|Overloads a default namespace prefix to be used with XPath and CSS expressions.|
|siblings|Returns the siblings nodes of the current selection.|
|slice|Slices the list of nodes by $offset and $length.|
|text|Returns the text of the first node of the list.|
|xpathLiteral|Converts string for XPath expressions.|



### HtmlPageCrawler::__clone  

**Description**

```php
 __clone (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPageCrawler::__get  

**Description**

```php
 __get (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPageCrawler::__toString  

**Description**

```php
 __toString (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPageCrawler::addClass  

**Description**

```php
public addClass (string $name)
```

Adds the specified class(es) to each element in the set of matched elements. 

 

**Parameters**

* `(string) $name`
: One or more space-separated classes to be added to the class attribute of each matched element.  

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::addHtmlFragment  

**Description**

```php
 addHtmlFragment (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPageCrawler::after  

**Description**

```php
public after (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Insert content, specified by the parameter, after each element in the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::append  

**Description**

```php
public append (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Insert HTML content as child nodes of each element after existing children 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`
: HTML code fragment or DOMNode to append  

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::appendTo  

**Description**

```php
public appendTo (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $element)
```

Insert every element in the set of matched elements to the end of the target. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $element`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> A new Crawler object containing all elements appended to the target elements


<hr />


### HtmlPageCrawler::before  

**Description**

```php
public before (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Insert content, specified by the parameter, before each element in the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::create  

**Description**

```php
public static create (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList|array $content)
```

Get an HtmlPageCrawler object from a HTML string, DOMNode, DOMNodeList or HtmlPageCrawler 

This is the equivalent to jQuery's $() function when used for wrapping DOMNodes or creating DOMElements from HTML code. 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList|array) $content`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPageCrawler::css  

**Description**

```php
public css (string $key, null|string $value)
```

Get one CSS style property of the first element or set it for all elements in the list 

Function is here for compatibility with jQuery; it is the same as getStyle() and setStyle() 

**Parameters**

* `(string) $key`
: The name of the style property  
* `(null|string) $value`
: The CSS value to set, or NULL to get the current value  

**Return Values**

`\HtmlPageCrawler|string`

> If no param is provided, returns the CSS styles of the first element


<hr />


### HtmlPageCrawler::getAttribute  

**Description**

```php
public getAttribute (string $name)
```

Returns the attribute value of the first node of the list. 

This is just an alias for attr() for naming consistency with setAttribute() 

**Parameters**

* `(string) $name`
: The attribute name  

**Return Values**

`string|null`

> The attribute value or null if the attribute does not exist


**Throws Exceptions**


`\InvalidArgumentException`
> When current node is empty

<hr />


### HtmlPageCrawler::getCombinedText  

**Description**

```php
public getCombinedText (void)
```

Get the combined text contents of each element in the set of matched elements, including their descendants. 

This is what the jQuery text() function does, contrary to the Crawler::text() method that returns only  
the text of the first node. 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### HtmlPageCrawler::getDOMDocument  

**Description**

```php
public getDOMDocument (void)
```

get ownerDocument of the first element 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\DOMDocument|null`




<hr />


### HtmlPageCrawler::getInnerHtml  

**Description**

```php
public getInnerHtml (void)
```

Alias for Crawler::html() for naming consistency with setInnerHtml() 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### HtmlPageCrawler::getStyle  

**Description**

```php
public getStyle (string $key)
```

get one CSS style property of the first element 

 

**Parameters**

* `(string) $key`
: name of the property  

**Return Values**

`string|null`

> value of the property


<hr />


### HtmlPageCrawler::hasClass  

**Description**

```php
public hasClass (string $name)
```

Determine whether any of the matched elements are assigned the given class. 

 

**Parameters**

* `(string) $name`

**Return Values**

`bool`




<hr />


### HtmlPageCrawler::insertAfter  

**Description**

```php
public insertAfter (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $element)
```

Insert every element in the set of matched elements after the target. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $element`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> A new Crawler object containing all elements appended to the target elements


<hr />


### HtmlPageCrawler::insertBefore  

**Description**

```php
public insertBefore (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $element)
```

Insert every element in the set of matched elements before the target. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $element`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> A new Crawler object containing all elements appended to the target elements


<hr />


### HtmlPageCrawler::isHtmlDocument  

**Description**

```php
public isHtmlDocument (void)
```

checks whether the first node contains a complete html document (as opposed to a document fragment) 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`bool`




<hr />


### HtmlPageCrawler::makeClone  

**Description**

```php
public makeClone (void)
```

Create a deep copy of the set of matched elements. 

Equivalent to clone() in jQuery (clone is not a valid PHP function name) 

**Parameters**

`This function has no parameters.`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPageCrawler::makeEmpty  

**Description**

```php
public makeEmpty (void)
```

Removes all child nodes and text from all nodes in set 

Equivalent to jQuery's empty() function which is not a valid function name in PHP 

**Parameters**

`This function has no parameters.`

**Return Values**

`\HtmlPageCrawler`

> $this


<hr />


### HtmlPageCrawler::prepend  

**Description**

```php
public prepend (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Insert content, specified by the parameter, to the beginning of each element in the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`
: HTML code fragment  

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::prependTo  

**Description**

```php
public prependTo (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $element)
```

Insert every element in the set of matched elements to the beginning of the target. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $element`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> A new Crawler object containing all elements prepended to the target elements


<hr />


### HtmlPageCrawler::remove  

**Description**

```php
public remove (void)
```

Remove the set of matched elements from the DOM. 

(as opposed to Crawler::clear() which detaches the nodes only from Crawler  
but leaves them in the DOM) 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPageCrawler::removeAttr  

**Description**

```php
public removeAttr (string $name)
```

Remove an attribute from each element in the set of matched elements. 

Alias for removeAttribute for compatibility with jQuery 

**Parameters**

* `(string) $name`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPageCrawler::removeAttribute  

**Description**

```php
public removeAttribute (string $name)
```

Remove an attribute from each element in the set of matched elements. 

 

**Parameters**

* `(string) $name`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPageCrawler::removeClass  

**Description**

```php
public removeClass (string $name)
```

Remove a class from each element in the list 

 

**Parameters**

* `(string) $name`

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::replaceAll  

**Description**

```php
public replaceAll (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $element)
```

Replace each target element with the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $element`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> A new Crawler object containing all elements appended to the target elements


<hr />


### HtmlPageCrawler::replaceWith  

**Description**

```php
public replaceWith (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Replace each element in the set of matched elements with the provided new content and return the set of elements that was removed. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::saveHTML  

**Description**

```php
public saveHTML (void)
```

Get the HTML code fragment of all elements and their contents. 

If the first node contains a complete HTML document return only  
the full code of this document. 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> HTML code (fragment)


<hr />


### HtmlPageCrawler::setAttribute  

**Description**

```php
public setAttribute (string $name, string $value)
```

Sets an attribute on each element 

 

**Parameters**

* `(string) $name`
* `(string) $value`

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::setInnerHtml  

**Description**

```php
public setInnerHtml (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Set the HTML contents of each element 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`
: HTML code fragment  

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::setStyle  

**Description**

```php
public setStyle (string $key, string $value)
```

set one CSS style property for all elements in the list 

 

**Parameters**

* `(string) $key`
: name of the property  
* `(string) $value`
: value of the property  

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::setText  

**Description**

```php
public setText (string $text)
```

Set the text contents of the matched elements. 

 

**Parameters**

* `(string) $text`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPageCrawler::toggleClass  

**Description**

```php
public toggleClass (string $classname)
```

Add or remove one or more classes from each element in the set of matched elements, depending the class’s presence. 

 

**Parameters**

* `(string) $classname`
: One or more classnames separated by spaces  

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::unwrap  

**Description**

```php
public unwrap (void)
```

Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::unwrapInner  

**Description**

```php
public unwrapInner (void)
```

Remove the matched elements, but promote the children to take their place. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::wrap  

**Description**

```php
public wrap (string|\HtmlPageCrawler|\DOMNode $wrappingElement)
```

Wrap an HTML structure around each element in the set of matched elements 

The HTML structure must contain only one root node, e.g.:  
Works: <div><div></div></div>  
Does not work: <div></div><div></div> 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode) $wrappingElement`

**Return Values**

`\HtmlPageCrawler`

> $this for chaining


<hr />


### HtmlPageCrawler::wrapAll  

**Description**

```php
public wrapAll (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Wrap an HTML structure around all elements in the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


**Throws Exceptions**


`\LogicException`


<hr />


### HtmlPageCrawler::wrapInner  

**Description**

```php
public wrapInner (string|\HtmlPageCrawler|\DOMNode|\DOMNodeList $content)
```

Wrap an HTML structure around the content of each element in the set of matched elements. 

 

**Parameters**

* `(string|\HtmlPageCrawler|\DOMNode|\DOMNodeList) $content`

**Return Values**

`\Wa72\HtmlPageDom\HtmlPageCrawler`

> $this for chaining


<hr />

