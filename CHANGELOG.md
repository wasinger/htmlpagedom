3.0.0
=====

2022-04-13

Changed some method signatures (added argument type hints and return types) in HtmlPageCrawler for compatibility with the base Crawler class from Symfony 6. So, this release is only compatible with Symfony 6 and up.

Otherwise there are no changes, so it does not require changes in code using this lib.

2.0.0
=====

2019-10-15

__BC BREAK__ for compatibility with Symfony 4.3 and up

- `HtmlPageCrawler::html()` is now just the parent `Crawler::html()` and acts as *getter* only.
  Setting HTML content via `HtmlPageCrawler::html($html)` is *not possible* any more,
  use `HtmlPageCrawler::setInnerHtml($html)` instead

- `HtmlPageCrawler::text()` is now just the parent `Crawler::text()` and acts as *getter* only
  that returns the text content from the *first* node only. For setting text content, use `HtmlPageCrawler::setText($text)` instead.
    
- `HtmlPageCrawler::attr()` is now just the parent `Crawler::attr()` and acts as *getter* only.
  For setting attributes use `HtmlPageCrawler::setAttribute($name, $value)` instead

- new method `HtmlPageCrawler::getCombinedText()` that returns the combined text from all nodes (as jQuery's `text()` function does and previous versions of `HtmlPageCrawler::text()` did)

- removed method `HtmlPageCrawler::isDisconnected()`


1.4.2
=====

2019-10-15

- undo deprecation of getInnerHtml()
- deprecate setter use of attr()
- deprecate isDisconnected()


1.4.1
=====

2019-06-28

- Bugfix: setText() should convert special chars. Closes #34.


1.4.0
=====

2019-05-17

Preparation for a smooth migration to 2.x / Symfony 4.3:
- deprecate setter use of html() and text(),
- deprecate getInnerHtml(),
- new methods setText() and getCombinedText()


1.3.2
=====

2019-04-18

- Mark this version as incompatible to Symfony DomCrawler 4.3


1.3
===

2016-10-06

- new method `unwrapInner` (thanks to [@ttk](https://github.com/ttk))

- it's now possible to get the number of nodes in the crawler using the
  `$crawler->length` property like in Javascript instead of `count($crawler)`


1.2
===

2015-11-06

- new methods `HtmlPage::minify()` and `HtmlPage::indent()` for compressing or nicely indenting the HTML document. These
  functions rely on the package `wa72/html-pretty-min` that is *suggested* in composer.json.

1.1
===

2015-05-20

- `text()` function now returns combined text of all elements in set (as jQuery does; previously only the nodeValue of 
  the first element was returned) and can act as a setter `text($string)` that sets the nodeValue of all elements to
  the specified string

- function `hasClass` now returns true if any of the elements in the Crawler has the specified class (previously,
  only the first element was checked). 

- new function `makeClone` as equivalent to jQuery's `clone` function ("clone" is not a valid function name in PHP).
  As previously, you can alternatively use PHP's clone operator: `$r = $c->makeClone()` is the same as `$r = clone $c`,
  but the new function allows chaining.

- new function `removeAttr` aliasing `removeAttribute` for compatibility with jQuery

- `appendTo`, `insertBefore`, `insertAfter`, and `replaceAll` now always return a new Crawler object containing
  the aggregate set of all elements appended to the target elements (this is the behavior of jQuery 1.9 and newer).
  
- `attr` function can now act as setter `attr($name, $value)` which is an alias for `setAttribute($name, $value)`
  (previously it accepted only one argument and was a getter equivalent to `getAttribute($name)` only, like it is
  in parent DomCrawler)
  
- `attr($name)` and `getAttribute($name)` now always return `null` if the attribute does not exist (previously, an empty
  string was returned when used with Symfony 2.3)

1.0
===
