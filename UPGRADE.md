Upgrade from 2.x to 3.0
-----------------------

Release 3.x is compatible only with Symfony 6, while older releases are compatible with Symfony up to 5.4.
Otherwise there are no changes in our API, so no changes should be required in your code using this lib. Just upgrade to Version 3 when you upgrade your project to Symfony 6 and all should be well.


Upgrade from 1.x to 2.0
------------------------

Several changes have been made to the public API in 2.0 in order to keep
compatibility with Symfony 4.3:

- `HtmlPageCrawler::html()` is now just the parent `Crawler::html()` and acts as *getter* only.
  Setting HTML content via `HtmlPageCrawler::html($html)` is *not possible* any more,
  use `HtmlPageCrawler::setInnerHtml($html)` instead

- `HtmlPageCrawler::text()` is now just the parent `Crawler::text()` and acts as *getter* only
  that returns the text content from the *first* node only. For setting text content, use
  `HtmlPageCrawler::setText($text)` instead.
   
- new method `HtmlPageCrawler::getCombinedText()` that returns the combined text from all nodes
  (as jQuery's `text()` function does and previous versions of `HtmlPageCrawler::text()` did)

- `HtmlPageCrawler::attr()` is now just the parent `Crawler::attr()` and acts as *getter* only.
  For setting attributes use `HtmlPageCrawler::setAttribute($name, $value)` 

- removed method `HtmlPageCrawler::isDisconnected()`

__To update your code, you have to:__

- replace all calls to `$MyCrawlerInstance->html($html)` used as *setter* by `$MyCrawlerInstance->setInnerHtml($html)`
- replace all calls to `$MyCrawlerInstance->attr($name, $value)` used as *setter* by `$MyCrawlerInstance->setAttribute($name, $value)`
- replace all calls to `$MyCrawlerInstance->text($text)` used as *setter* by `$MyCrawlerInstance->setText($text)`
- replace all calls to `$MyCrawlerInstance->text()` (i.e. every call to `text()` not preceded by `first()`) by `$MyCrawlerInstance->getCombinedText()`
- replace all calls to `$MyCrawlerInstance->first()->text()` by `$MyCrawlerInstance->text()`
