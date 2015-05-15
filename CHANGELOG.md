Master
=================

- `attr` function can now act as setter `attr($name, $value)` which is an alias for `setAttribute($name, $value)`
  (previously it accepted only one argument and was a getter equivalent to `getAttribute($name)` only, like it is
  in parent DomCrawler)
- `attr($name)` and `getAttribute($name)` now always return `null` if the attribute does not exist (previously, an empty
  string was returned when used with Symfony 2.3)

1.0
===