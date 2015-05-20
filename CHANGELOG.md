1.1
===
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