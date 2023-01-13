# Wa72\HtmlPageDom\HtmlPage  

This class represents a complete HTML document.

It offers convenience functions for getting and setting elements of the document
such as setTitle(), getTitle(), setMeta($name, $value), getBody().

It uses HtmlPageCrawler to navigate and manipulate the DOM tree.  

## Implements:
Stringable



## Methods

| Name | Description |
|------|-------------|
|[__clone](#htmlpage__clone)||
|[__construct](#htmlpage__construct)||
|[__toString](#htmlpage__tostring)||
|[filter](#htmlpagefilter)|Filter nodes by using a CSS selector|
|[filterXPath](#htmlpagefilterxpath)|Filter nodes by XPath expression|
|[getBaseHref](#htmlpagegetbasehref)|Get the href attribute from the base tag, null if not present in document|
|[getBody](#htmlpagegetbody)|Get the document's body wrapped in a HtmlPageCrawler instance|
|[getBodyNode](#htmlpagegetbodynode)|Get the document's body as DOMElement|
|[getCrawler](#htmlpagegetcrawler)|Get a HtmlPageCrawler object containing the root node of the HTML document|
|[getDOMDocument](#htmlpagegetdomdocument)|Get a DOMDocument object for the HTML document|
|[getElementById](#htmlpagegetelementbyid)|Get an element in the document by it's id attribute|
|[getHead](#htmlpagegethead)|Get the document's HEAD section wrapped in a HtmlPageCrawler instance|
|[getHeadNode](#htmlpagegetheadnode)|Get the document's HEAD section as DOMElement|
|[getMeta](#htmlpagegetmeta)|Get the content attribute of a meta tag with the specified name attribute|
|[getTitle](#htmlpagegettitle)|Get the page title of the HTML document|
|[indent](#htmlpageindent)|indent the HTML document|
|[minify](#htmlpageminify)|minify the HTML document|
|[removeMeta](#htmlpageremovemeta)|Remove all meta tags with the specified name attribute|
|[save](#htmlpagesave)|Save this document to a HTML file or return HTML code as string|
|[setBaseHref](#htmlpagesetbasehref)|Set the base tag with href attribute set to parameter $url|
|[setHtmlById](#htmlpagesethtmlbyid)|Sets innerHTML content of an element specified by elementId|
|[setMeta](#htmlpagesetmeta)|Set a META tag with specified 'name' and 'content' attributes|
|[setTitle](#htmlpagesettitle)|Sets the page title of the HTML document|
|[trimNewlines](#htmlpagetrimnewlines)|remove newlines from string and minimize whitespace (multiple whitespace characters replaced by one space)|




### HtmlPage::__clone  

**Description**

```php
 __clone (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPage::__construct  

**Description**

```php
 __construct (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPage::__toString  

**Description**

```php
 __toString (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### HtmlPage::filter  

**Description**

```php
public filter (string $selector)
```

Filter nodes by using a CSS selector 

 

**Parameters**

* `(string) $selector`
: CSS selector  

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::filterXPath  

**Description**

```php
public filterXPath (string $xpath)
```

Filter nodes by XPath expression 

 

**Parameters**

* `(string) $xpath`
: XPath expression  

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::getBaseHref  

**Description**

```php
public getBaseHref (void)
```

Get the href attribute from the base tag, null if not present in document 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`null|string`




<hr />


### HtmlPage::getBody  

**Description**

```php
public getBody (void)
```

Get the document's body wrapped in a HtmlPageCrawler instance 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::getBodyNode  

**Description**

```php
public getBodyNode (void)
```

Get the document's body as DOMElement 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\DOMElement`




<hr />


### HtmlPage::getCrawler  

**Description**

```php
public getCrawler (void)
```

Get a HtmlPageCrawler object containing the root node of the HTML document 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::getDOMDocument  

**Description**

```php
public getDOMDocument (void)
```

Get a DOMDocument object for the HTML document 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\DOMDocument`




<hr />


### HtmlPage::getElementById  

**Description**

```php
public getElementById (string $id)
```

Get an element in the document by it's id attribute 

 

**Parameters**

* `(string) $id`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::getHead  

**Description**

```php
public getHead (void)
```

Get the document's HEAD section wrapped in a HtmlPageCrawler instance 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\HtmlPageCrawler`




<hr />


### HtmlPage::getHeadNode  

**Description**

```php
public getHeadNode (void)
```

Get the document's HEAD section as DOMElement 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\DOMElement`




<hr />


### HtmlPage::getMeta  

**Description**

```php
public getMeta (string $name)
```

Get the content attribute of a meta tag with the specified name attribute 

 

**Parameters**

* `(string) $name`

**Return Values**

`null|string`




<hr />


### HtmlPage::getTitle  

**Description**

```php
public getTitle (void)
```

Get the page title of the HTML document 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`null|string`




<hr />


### HtmlPage::indent  

**Description**

```php
public indent (array $options)
```

indent the HTML document 

 

**Parameters**

* `(array) $options`
: Options passed to PrettyMin::__construct()  

**Return Values**

`\HtmlPage`




**Throws Exceptions**


`\Exception`


<hr />


### HtmlPage::minify  

**Description**

```php
public minify (array $options)
```

minify the HTML document 

 

**Parameters**

* `(array) $options`
: Options passed to PrettyMin::__construct()  

**Return Values**

`\HtmlPage`




**Throws Exceptions**


`\Exception`


<hr />


### HtmlPage::removeMeta  

**Description**

```php
public removeMeta (string $name)
```

Remove all meta tags with the specified name attribute 

 

**Parameters**

* `(string) $name`

**Return Values**

`void`


<hr />


### HtmlPage::save  

**Description**

```php
public save (string $filename)
```

Save this document to a HTML file or return HTML code as string 

 

**Parameters**

* `(string) $filename`
: If provided, output will be saved to this file, otherwise returned  

**Return Values**

`string|void`




<hr />


### HtmlPage::setBaseHref  

**Description**

```php
public setBaseHref (string $url)
```

Set the base tag with href attribute set to parameter $url 

 

**Parameters**

* `(string) $url`

**Return Values**

`void`


<hr />


### HtmlPage::setHtmlById  

**Description**

```php
public setHtmlById (string $elementId, string $html)
```

Sets innerHTML content of an element specified by elementId 

 

**Parameters**

* `(string) $elementId`
* `(string) $html`

**Return Values**

`void`


<hr />


### HtmlPage::setMeta  

**Description**

```php
public setMeta ( $name,  $content)
```

Set a META tag with specified 'name' and 'content' attributes 

 

**Parameters**

* `() $name`
* `() $content`

**Return Values**

`void`


<hr />


### HtmlPage::setTitle  

**Description**

```php
public setTitle (string $title)
```

Sets the page title of the HTML document 

 

**Parameters**

* `(string) $title`

**Return Values**

`void`


<hr />


### HtmlPage::trimNewlines  

**Description**

```php
public static trimNewlines (string $string)
```

remove newlines from string and minimize whitespace (multiple whitespace characters replaced by one space) 

useful for cleaning up text retrieved by HtmlPageCrawler::text() (nodeValue of a DOMNode) 

**Parameters**

* `(string) $string`

**Return Values**

`string`




<hr />

