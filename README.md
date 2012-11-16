HtmlPageDom
===========

HtmlPageDom is a PHP library for easy manipulation of HTML documents using DOM.
It requires [DomCrawler from Symfony2 components](https://github.com/symfony/DomCrawler) for traversing 
the DOM tree and extends it by adding methods for manipulating the DOM tree of HTML documents.

HtmlPageDom consists of two classes:

-   *HtmlPage* represents one HTML document and offers convenience functions like setTitle($title),
    setMeta('description', $description), getBody()

-   *HtmlPageCrawler* is used by HtmlPage for selecting and manipulating elements of the document's DOM tree.
    It extends Symfony\Components\DomCrawler by adding manipulation functions such as setInnerHtml(), addClass() or css().
    It's like jQuery for PHP: you can select elements of an HTML page using CSS selectors and change their attributes and content.

Requirements
------------

-   PHP 5.3+
-   [Symfony\Components\DomCrawler](https://github.com/symfony/DomCrawler)
-   [Symfony\Components\CssSelector](https://github.com/symfony/CssSelector)


Usage
-----

```php
// create a new HtmlPage object with an empty HTML skeleton
$page = new \Wa72\HtmlPageDom\HtmlPage();

// or create a HtmlPage object from an existing page
$page = new \Wa72\HtmlPageDom\HtmlPage(file_get_contents('http://www.heise.de'));

// get or set page title
echo $page->getTitle();
$page->setTitle('New page title');
echo $page->getTitle();


// add HTML content
$page->filter('body')->setInnerHtml('<div id="#content"><h1>This is the headline</h1><p class="text">This is a paragraph</p></div>');

// select elements by css selector
$h1 = $page->filter('#content h1');
$p = $page->filter('p.text');

// change attributes and content of an element
$h1->addClass('headline')->css('margin-top', '10px')->setInnerHtml('This is the <em>new</em> headline');

$p->removeClass('text')->append('<br>There is more than one line in this paragraph');

// add a new paragraph to div#content
$page->filter('#content')->append('<p>This is a new paragraph.</p>');

// add a class and some attribute to all paragraphs
$page->filter('p')->addClass('newclass')->setAttribute('data-foo', 'bar');


// get HTML content of an element
echo $page->filter('#content')->saveHTML();

// output the whole HTML page
echo $page->save();
```
