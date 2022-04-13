<?php
namespace Wa72\HtmlPageDom\Tests;

use Wa72\HtmlPageDom\HtmlPageCrawler;
use PHPUnit\Framework\TestCase;

class HtmlPageCrawlerTest extends TestCase
{
    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::__construct
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::filter
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::getFirstNode
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::nodeName
     */
    public function testHtmlPageCrawler()
    {
        $c = new HtmlPageCrawler;
        $c->addHtmlContent('<!doctype html><html><body><div id="content"><h1>Title</h1></div></body></html>');
        $title = $c->filter('#content > h1');

        $this->assertInstanceOf('\Wa72\HtmlPageDom\HtmlPageCrawler', $title);
        $this->assertInstanceOf('\DOMNode', $title->getNode(0));
        $this->assertEquals('h1', $title->nodeName());
    }

    /**
     *
     *
     * @param $string
     * @return string
     */
    private function _ignoreNewlines($string)
    {
        return str_replace("\n", '', $string);
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setInnerHtml
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::prepend
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::makeEmpty
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setAttribute
     */
    public function testManipulationFunctions()
    {
        $c = new HtmlPageCrawler;
        $c->addHtmlContent('<!doctype html><html><body><div id="content"><h1>Title</h1></div></body></html>');

        $content = $c->filter('#content');
        $content->append('<p>Das ist ein Testabsatz');
        $this->assertEquals("<h1>Title</h1><p>Das ist ein Testabsatz</p>", $this->_ignoreNewlines($content->html()));

        $content->setInnerHtml('<p>Ein neuer <b>Inhalt</p>');
        $this->assertEquals('<p>Ein neuer <b>Inhalt</b></p>', $content->html());

        $content->prepend('<h1>Neue Überschrift');
        $this->assertEquals('<h1>Neue Überschrift</h1><p>Ein neuer <b>Inhalt</b></p>', $content->html());

        $h1 = $content->filter('h1');
        $this->assertEquals('Neue Überschrift', $h1->text());

        $b = $content->filter('b');
        $this->assertEquals('Inhalt', $b->text());

        $b2 = $c->filter('#content p b');
        $this->assertEquals('Inhalt', $b2->text());

        $content->append('<p class="a2">Zweiter Absatz</p>');
        $content->append('<p class="a3"><b>Dritter Absatz</b> und noch mehr Text</p>');

        $a3 = $content->filter('p.a3');
        $this->assertEquals('<b>Dritter Absatz</b> und noch mehr Text', $a3->html());

        $a3b = $a3->filter('b');
        $this->assertEquals('Dritter Absatz', $a3b->text());

        $body = $c->filter('body');
        $this->assertEquals('<div id="content"><h1>Neue Überschrift</h1><p>Ein neuer <b>Inhalt</b></p><p class="a2">Zweiter Absatz</p><p class="a3"><b>Dritter Absatz</b> und noch mehr Text</p></div>', $this->_ignoreNewlines($body->html()));

        $paragraphs = $c->filter('p');
        $this->assertEquals(3, count($paragraphs));

        $paragraphs->append('<span class="appended">.</span>');
        $this->assertEquals('<p>Ein neuer <b>Inhalt</b><span class="appended">.</span></p><p class="a2">Zweiter Absatz<span class="appended">.</span></p><p class="a3"><b>Dritter Absatz</b> und noch mehr Text<span class="appended">.</span></p>', $c->filter('p')->saveHTML());

        $body->makeEmpty();
        $this->assertEmpty($body->html());

        $body->setAttribute('class', 'mybodyclass');
        $this->assertEquals('mybodyclass', $body->attr('class'));
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::append
     */
    public function testAppend()
    {
        // Testing append string to several elements
        $c = new HtmlPageCrawler('<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>');
        $c->filter('p')->append('<br>Appended Text');
        $this->assertEquals('<p>Paragraph 1<br>Appended Text</p><p>Paragraph 2<br>Appended Text</p><p>Paragraph 3<br>Appended Text</p>', $c->saveHTML());

        // Testing append HtmlPageCrawler to several elements
        $c = new HtmlPageCrawler('<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>');
        $c->filter('p')->append(new HtmlPageCrawler('<br>Appended Text'));
        $this->assertEquals('<p>Paragraph 1<br>Appended Text</p><p>Paragraph 2<br>Appended Text</p><p>Paragraph 3<br>Appended Text</p>', $c->saveHTML());

        // Testing append DOMNode to several elements
        $c = new HtmlPageCrawler('<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>');
        $app = $c->getDOMDocument()->createElement('span', 'Appended Text');
        $c->filter('p')->append($app);
        $this->assertEquals('<p>Paragraph 1<span>Appended Text</span></p><p>Paragraph 2<span>Appended Text</span></p><p>Paragraph 3<span>Appended Text</span></p>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><span>Append Self</span></div>');
        $c->filter('#content')->append($c->filter('span'));
        $this->assertEquals('<div id="content"><span>Append Self</span></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::appendTo
     */
    public function testAppendTo()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1><em>Big</em></div>');
        $c->filter('em')->appendTo($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Title<em>Big</em></h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Self Title</h1></div>');
        $c->filter('h1')->appendTo($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Self Title</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::isHtmlDocument
     */
    public function testIsHtmlDocument()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="content"><h1>Title</h1></div></body></html>');
        $c = new HtmlPageCrawler($dom);

        $this->assertTrue($c->isHtmlDocument());

        $t = $c->filter('body');
        $this->assertFalse($t->isHtmlDocument());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $this->assertFalse($c->isHtmlDocument());

        $c = new HtmlPageCrawler('<html><body><div id="content"><h1>Title</h1></div></body></html>');
        $this->assertTrue($c->isHtmlDocument());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::saveHTML
     */
    public function testSaveHTML()
    {
        $html = "<!DOCTYPE html><html><body><h1>Title</h1><p>Paragraph 1</p><p>Paragraph 2</p></body></html>";
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        $c = new HtmlPageCrawler($dom);
        $this->assertEquals($html, $this->_ignoreNewlines($c->saveHTML()));
        $ps = $c->filter('p');
        $this->assertEquals('<p>Paragraph 1</p><p>Paragraph 2</p>', $ps->saveHTML());
        $t = $c->filter('h1');
        $this->assertEquals('<h1>Title</h1>', $t->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $this->assertEquals('<div id="content"><h1>Title</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::css
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::getStyle
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setStyle
     */
    public function testCss()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="content"><h1 style=" margin-top:
         10px;border-bottom:  1px solid red">Title</h1></div></body></html>');
        $c = new HtmlPageCrawler($dom);
        $t = $c->filter('h1');
        $this->assertEquals('10px', $t->css('margin-top'));
        $this->assertEquals('1px solid red', $t->css('border-bottom'));
        $t->css('margin-bottom', '20px');
        $this->assertEquals('20px', $t->css('margin-bottom'));
        $this->assertEquals('10px', $t->getStyle('margin-top'));
        $this->assertEquals('<h1 style="margin-top: 10px;border-bottom: 1px solid red;margin-bottom: 20px;">Title</h1>', $t->saveHTML());
        $t->setStyle('border-bottom', '');
        $this->assertEquals('<h1 style="margin-top: 10px;margin-bottom: 20px;">Title</h1>', $t->saveHTML());
        $t->setStyle('padding-top', '0');
        $this->assertEquals('<h1 style="margin-top: 10px;margin-bottom: 20px;padding-top: 0;">Title</h1>', $t->saveHTML());
        $this->assertEquals('0', $t->getStyle('padding-top'));
        $this->assertNull($t->getStyle('border-bottom'));
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::addClass
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::removeClass
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::hasClass
     */
    public function testClasses()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="content"><h1 class="style_class">Title</h1></div></body></html>');
        $c = new HtmlPageCrawler($dom);
        $t = $c->filter('h1');
        $t->addClass('ueberschrift');
        $t->addClass('nochneklasse');
        $t->addClass('style_class');
        $this->assertEquals('<h1 class="style_class ueberschrift nochneklasse">Title</h1>', $t->saveHTML());
        $this->assertTrue($t->hasClass('ueberschrift'));
        $this->assertTrue($t->hasClass('nochneklasse'));
        $this->assertTrue($t->hasClass('style_class'));
        $t->removeClass('nochneklasse');
        $this->assertTrue($t->hasClass('ueberschrift'));
        $this->assertFalse($t->hasClass('nochneklasse'));
        $t->addClass('class1 class2');
        $this->assertTrue($t->hasClass('class1'));
        $this->assertTrue($t->hasClass('class2'));

        $c1 = new HtmlPageCrawler('<p class="a"></p><p class="b"></p><p class="c"></p>');
        $this->assertTrue($c1->hasClass('b'));
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::addContent
     */
    public function testAddContent()
    {
        $c = new HtmlPageCrawler();
        $c->addContent('<html><body><div id="content"><h1>Title</h1></div></body>');
        $this->assertEquals(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'
            . "" . '<html><body><div id="content"><h1>Title</h1></div></body></html>' . "",
            $this->_ignoreNewlines($c->saveHTML())
        );

        $c = new HtmlPageCrawler();
        $c->addContent('<div id="content"><h1>Title');
        $this->assertEquals('<div id="content"><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler();
        $c->addContent('<p>asdf<p>asdfaf</p>');
        $this->assertEquals(2, count($c));
        $this->assertEquals('<p>asdf</p><p>asdfaf</p>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::before
     */
    public function testBefore()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->before('<p>Text before h1</p>');
        $this->assertEquals('<div id="content"><p>Text before h1</p><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->before(new HtmlPageCrawler('<p>Text before h1</p><p>and more text before</p>'));
        $this->assertEquals('<div id="content"><p>Text before h1</p><p>and more text before</p><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Self Before</h1></div>');
        $c->filter('h1')->before($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Self Before</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::insertBefore
     */
    public function testInsertBefore()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1><p>Text before h1</p></div>');
        $c->filter('p')->insertBefore($c->filter('h1'));
        $this->assertEquals('<div id="content"><p>Text before h1</p><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Self Insert Before Title</h1><p>Text after h1</p></div>');
        $c->filter('h1')->insertBefore($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Self Insert Before Title</h1><p>Text after h1</p></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::after
     */
    public function testAfter()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->after('<p>Text after h1</p>');
        $this->assertEquals('<div id="content"><h1>Title</h1><p>Text after h1</p></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1><h1>Title2</h1></div>');
        $c->filter('h1')->after(new HtmlPageCrawler('<p>Text after h1</p><p>and more text after</p>'));
        $this->assertEquals('<div id="content"><h1>Title</h1><p>Text after h1</p><p>and more text after</p><h1>Title2</h1><p>Text after h1</p><p>and more text after</p></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Self After</h1></div>');
        $c->filter('h1')->after($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Self After</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::insertAfter
     */
    public function testInsertAfter()
    {
        $c = new HtmlPageCrawler('<div id="content"><p>Text after h1</p><h1>Title</h1></div>');
        $c->filter('p')->insertAfter($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Title</h1><p>Text after h1</p></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><p>Text before h1</p><h1>Self Insert After Title</h1></div>');
        $c->filter('h1')->insertAfter($c->filter('h1'));
        $this->assertEquals('<div id="content"><p>Text before h1</p><h1>Self Insert After Title</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::prepend
     */
    public function testPrepend()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('#content')->prepend('<p>Text before h1</p>');
        $this->assertEquals('<div id="content"><p>Text before h1</p><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"></div>');
        $c->filter('#content')->prepend(new HtmlPageCrawler('<p>Text before h1</p><p>and more text before</p>'));
        $this->assertEquals('<div id="content"><p>Text before h1</p><p>and more text before</p></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><span>Prepend Self</span></div>');
        $c->filter('#content')->prepend($c->filter('span'));
        $this->assertEquals('<div id="content"><span>Prepend Self</span></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::prependTo
     */
    public function testPrependTo()
    {
        $c = new HtmlPageCrawler('<div id="content"><p>Text before</p></div>');
        $c->filter('p')->prependTo('Text');
        $this->assertEquals('<div id="content"><p>Text before</p></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('#content')->prependTo(new HtmlPageCrawler('<p>paragraph</p>'));
        $this->assertEquals('<div id="content"><h1>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1><em>Big</em></div>');
        $c->filter('em')->prependTo($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1><em>Big</em>Title</h1></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Self Title</h1></div>');
        $c->filter('h1')->prependTo($c->filter('h1'));
        $this->assertEquals('<div id="content"><h1>Self Title</h1></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::wrap
     */
    public function testWrap()
    {
        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->wrap('<div class="innercontent">');
        $this->assertEquals('<div id="content"><div class="innercontent"><h1>Title</h1></div></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->wrap('<div class="ic">asdf<div class="a1"><div class="a2"></div></div></div></div>');
        $this->assertEquals('<div id="content"><div class="ic">asdf<div class="a1"><div class="a2"><h1>Title</h1></div></div></div></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('<div id="content"><h1>Title</h1></div>');
        $c->filter('h1')->wrap('<div class="ic">asdf</div><div>jkl</div>'); // wrap has more than 1 root element
        $this->assertEquals('<div id="content"><div class="ic">asdf<h1>Title</h1></div></div>', $c->saveHTML()); // only first element is used

        // Test for wrapping multiple nodes
        $c = new HtmlPageCrawler('<div id="content"><p>p1</p><p>p2</p></div>');
        $c->filter('p')->wrap('<div class="p"></div>');
        $this->assertEquals('<div id="content"><div class="p"><p>p1</p></div><div class="p"><p>p2</p></div></div>', $c->saveHTML());

        $c = new HtmlPageCrawler('plain text node');
        $c->wrap('<div class="ic"></div>');
        $this->assertEquals('<div class="ic">plain text node</div>', $c->ancestors()->eq(0)->saveHTML());

        $c = HtmlPageCrawler::create('<div>');
        $m = HtmlPageCrawler::create('message 1')->appendTo($c);
        $m->wrap('<p>');
        $m = HtmlPageCrawler::create('message 2')->appendTo($c);
        $m->wrap('<p>');
        $this->assertEquals('<div><p>message 1</p><p>message 2</p></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::replaceWith
     */
    public function testReplaceWith()
    {
        $c = HtmlPageCrawler::create('<div id="content"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div>');
        $oldparagraphs = $c->filter('p')->replaceWith('<div>newtext 1</div><div>newtext 2</div>');
        $this->assertEquals('<div id="content"><div>newtext 1</div><div>newtext 2</div><div>newtext 1</div><div>newtext 2</div><div>newtext 1</div><div>newtext 2</div></div>', $c->saveHTML());
        $this->assertEquals('<p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p>', $oldparagraphs->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::replaceAll
     */
    public function testReplaceAll()
    {
        $c = HtmlPageCrawler::create('<div id="content"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div>');
        $new = HtmlPageCrawler::create('<div>newtext 1</div><div>newtext 2</div>');
        $new->replaceAll($c->filter('p'));
        $this->assertEquals('<div id="content"><div>newtext 1</div><div>newtext 2</div><div>newtext 1</div><div>newtext 2</div><div>newtext 1</div><div>newtext 2</div></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::wrapAll
     */
    public function testWrapAll()
    {
        $c = HtmlPageCrawler::create('<div id="content"><div>Before</div><p>Absatz 1</p><div>Inner</div><p>Absatz 2</p><p>Absatz 3</p><div>After</div></div>');
        $c->filter('p')->wrapAll('<div class="a">');
        $this->assertEquals('<div id="content"><div>Before</div><div class="a"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div><div>Inner</div><div>After</div></div>', $c->saveHTML());

        // Test for wrapping with elements that have children
        $c = HtmlPageCrawler::create('<div id="content"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div>');
        $c->filter('p')->wrapAll('<article><section><div class="a"></div></section></article>');
        $this->assertEquals('<div id="content"><article><section><div class="a"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div></section></article></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::wrapInner
     */
    public function testWrapInner()
    {
        $c = HtmlPageCrawler::create('<div id="content"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div>');
        $c->wrapInner('<div class="a">');
        $this->assertEquals('<div id="content"><div class="a"><p>Absatz 1</p><p>Absatz 2</p><p>Absatz 3</p></div></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::unwrap
     */
    public function testUnwrap()
    {
        $c = HtmlPageCrawler::create('<div id="content"><div>Before</div><div class="a"><p>Absatz 1</p></div><div>After</div></div>');
        $p = $c->filter('p');
        $p->unwrap();
        $this->assertEquals('<div id="content"><div>Before</div><p>Absatz 1</p><div>After</div></div>', $c->saveHTML());
    }
    
    public function testUnwrapInnerOnDOMElementExeption()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('DOMElement does not have a parent DOMElement node.');
        
        $c = HtmlPageCrawler::create('<div id="content"></div>');
        $p = $c->filter('div#content');
        $p->unwrapInner();
        $p->unwrapInner();
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::unwrapInner
     */
    public function testUnwrapInner()
    {
        $c = HtmlPageCrawler::create('<div id="content"><div>Before</div><div class="a"><p>Absatz 1</p></div><div>After</div></div>');
        $p = $c->filter('div.a');
        $p->unwrapInner();
        $this->assertEquals('<div id="content"><div>Before</div><p>Absatz 1</p><div>After</div></div>', $c->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::toggleClass
     */
    public function testToggleClass()
    {
        $c = HtmlPageCrawler::create('<div id="1" class="a c"><div id="2" class="b c"></div></div>');
        $c->filter('div')->toggleClass('a d')->toggleClass('b');
        $this->assertEquals('<div id="1" class="c d b"><div id="2" class="c a d"></div></div>', $c->saveHTML());
    }

    public function testRemove()
    {
        // remove every third td in tbody
        $html = <<<END
<table>
    <thead>
    <tr>
        <th>A</th>
        <th>B</th>
    </tr>
    </thead>
    <tbody>
    <tr class="r1">
        <td class="c11">16.12.2013</td>
        <td class="c12">asdf asdf</td>
        <td class="c13">&nbsp;</td>
    </tr>
    <tr class="r2">
        <td class="c21">02.12.2013 16:30</td>
        <td class="c22">asdf asdf</td>
        <td class="c23">&nbsp;</td>
    </tr>
    <tr class="r3">
        <td class="c31">25.11.2013 16:30</td>
        <td class="c32">asdf asdf</td>
        <td class="c33">&nbsp;</td>
    </tr>
    <tr class="r4">
        <td class="c41">18.11.2013 16:30</td>
        <td class="c42">asdf asdf</td>
        <td class="c43">&nbsp;</td>
    </tr>
    <tr class="r5">
        <td class="c51">24.10.2013 16:30</td>
        <td class="c52">asdf asdf</td>
        <td class="c53">&nbsp;</td>
    </tr>
    <tr class="r6">
        <td class="c61">10.10.2013 16:30</td>
        <td class="c62">asdf asdf</td>
        <td class="c63">&nbsp;</td>
    </tr>
</table>
END;
        $c = HtmlPageCrawler::create($html);
        $this->assertEquals(1, count($c->filter('td.c23')));
        $tbd = $c->filter('table > tbody > tr > td')
            ->reduce(
                function ($c, $j) {
                    if (($j+1) % 3 == 0) {
                        return true;
                    }
                    return false;
                }
            );
        $this->assertEquals(6, count($tbd));
        $tbd->remove();
        $this->assertEquals(0, count($tbd));
        $this->assertEquals(0, count($c->filter('td.c23')));
    }

    public function testUTF8Characters()
    {
        $text = file_get_contents(__DIR__ . '/utf8.html');
        $c = HtmlPageCrawler::create($text);

        $expected =<<< END
<p style="margin: 0cm 0cm 0pt;"><span>Die Burse&nbsp;wurde unmittelbar (1478 bis 1482) nach der Universit&auml;tsgr&uuml;ndung als Studentenwohnhaus und -lehranstalt errichtet. Hier lehrte der Humanist und Reformator Philipp Melanchthon bis zu seiner Berufung nach Wittenberg 1518, an ihn erinnert eine Gedenktafel. 1803 bis 1805 wurde das Geb&auml;ude im Stil des Klassizismus zum ersten T&uuml;binger Klinikum umgebaut. Einer der ersten Patienten war Friedrich H&ouml;lderlin, der nach einer 231 Tage dauernden Behandlung am 3. Mai 1807 als unheilbar entlassen wurde.</span></p><p style="margin: 0cm 0cm 0pt;"><span>Einst Badeanstalt vor der Stadtmauer. Wer durch das kleine Stadttor geht, hat &ndash; r&uuml;ckw&auml;rts gewandt &ndash; einen guten Blick auf die Stadtbefestigung mit "Pechnasen" und Spuren des alten Wehrgangs.</span></p>
END;

        $this->assertEquals($expected, $c->filter('p')->saveHTML());
    }

    public function testAttr()
    {
        $c = HtmlPageCrawler::create('<div>');
        $this->assertNull($c->attr('data-foo'));
        $c->setAttribute('data-foo', 'bar');
        $this->assertEquals('bar', $c->attr('data-foo'));
        $this->assertEquals('bar', $c->getAttribute('data-foo'));
        $c->removeAttribute('data-foo');
        $this->assertNull($c->attr('data-foo'));
        $c->setAttribute('data-foo', 'bar');
        $this->assertEquals('bar', $c->attr('data-foo'));
        // getAttribute is just an alias to attr() and should provide the same result
        $this->assertEquals('bar', $c->getAttribute('data-foo'));
        $c->removeAttr('data-foo');
        $this->assertNull($c->attr('data-foo'));

    }
    
    public function testAttrOnInvalidNodeList()
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = HtmlPageCrawler::create(null);
        $c->attr('data-foo');
    }

    public function testSetInnerHtml()
    {
        $html = HtmlPageCrawler::create('<h1>Title</h1>');
        $this->assertInstanceOf('Wa72\HtmlPageDom\HtmlPageCrawler', $html->setInnerHtml('<h2>Title</h2>'));
        $this->assertEquals('<h2>Title</h2>', $html->html());
        // getInnerHtml is just an alias for html() and should provide the same result
        $this->assertEquals('<h2>Title</h2>', $html->getInnerHtml());
    }

    public function testToString()
    {
        $html = HtmlPageCrawler::create('<h2>Title</h2>');
        $this->assertEquals('<h2>Title</h2>', (string) $html);
    }

    public function testGetDOMDocument()
    {
        $html = HtmlPageCrawler::create('<h2>Title</h2>');
        $this->assertInstanceOf('\DOMDocument', $html->getDOMDocument());
    }

    public function testAddOnCrawlerInstance()
    {
        $html = HtmlPageCrawler::create('<h1>Title</h1>');
        $html->add($html);
        $this->assertEquals('<h1>Title</h1>', (string) $html);
    }

    public function testReturnValues()
    {
        // appendTo, insertBefore, insertAfter, replaceAll should always return new Crawler objects
        // see http://jquery.com/upgrade-guide/1.9/#appendto-insertbefore-insertafter-and-replaceall

        $c1 = HtmlPageCrawler::create('<h1>Headline</h1>');
        $c2 = HtmlPageCrawler::create('<p>1</p><p>2</p><p>3</p>');
        $c3 = HtmlPageCrawler::create('<span>asdf</span>');

        $r1 = $c3->appendTo($c1);
        $this->assertNotEquals(spl_object_hash($c3), spl_object_hash($r1));

        $r2 = $c3->insertBefore($c1);
        $this->assertNotEquals(spl_object_hash($c3), spl_object_hash($r2));

        $r3 = $c3->insertAfter($c1);
        $this->assertNotEquals(spl_object_hash($c3), spl_object_hash($r3));

        $r4 = $c3->replaceAll($c1);
        $this->assertNotEquals(spl_object_hash($c3), spl_object_hash($r4));


        $r1 = $c3->appendTo($c2);
        $this->assertNotEquals(spl_object_hash($c2), spl_object_hash($r1));

        $r2 = $c3->insertBefore($c2);
        $this->assertNotEquals(spl_object_hash($c2), spl_object_hash($r2));

        $r3 = $c3->insertAfter($c2);
        $this->assertNotEquals(spl_object_hash($c2), spl_object_hash($r3));

        $r4 = $c3->replaceAll($c2);
        $this->assertNotEquals(spl_object_hash($c2), spl_object_hash($r4));

    }

    public function testDisconnectedNodes()
    {
        // if after(), before() or replaceWith() is called on a node without parent,
        // the unmodified Crawler object should be returned
        //
        // see http://jquery.com/upgrade-guide/1.9/#after-before-and-replacewith-with-disconnected-nodes
        $c = HtmlPageCrawler::create('<div>abc</div>');
        $r = HtmlPageCrawler::create('<div>def</div>');

        $r1 = $c->after($r);
        $this->assertEquals(spl_object_hash($r1), spl_object_hash($c));
        $this->assertEquals(count($r1), count($c));

        $r2 = $c->before($r);
        $this->assertEquals(spl_object_hash($r2), spl_object_hash($c));
        $this->assertEquals(count($r2), count($c));

        $r3 = $c->replaceWith($r);
        $this->assertEquals(spl_object_hash($r3), spl_object_hash($c));
        $this->assertEquals(count($r3), count($c));
    }

    public function testClone()
    {
        $c = HtmlPageCrawler::create('<div><p class="x">asdf</p></div>');
        $p = $c->filter('p');

        $p1 = $p->makeClone();
        $this->assertNotEquals(spl_object_hash($p), spl_object_hash($p1));
        $this->assertTrue($p1->hasClass('x'));
        $p1->removeClass('x');
        $this->assertTrue($p->hasClass('x'));
        $this->assertFalse($p1->hasClass('x'));
        $p->after($p1);
        $this->assertEquals('<div><p class="x">asdf</p><p class="">asdf</p></div>', $c->saveHTML());
    }

    public function testGetCombinedText()
    {
        $c = HtmlPageCrawler::create('<p>abc</p><p>def</p>');
        $this->assertEquals('abcdef', $c->getCombinedText());
        $c->setText('jklo');
        $this->assertEquals('jklojklo', $c->getCombinedText());
    }

    public function testSetText()
    {
        $c = HtmlPageCrawler::create('<div>&quot;</div>');
        $this->assertEquals('"', $c->text());
        $c->setText('&');
        $this->assertEquals('&', $c->text());
    }

    public function testMagicGet()
    {
        // $crawler->length should give us the number of nodes in the crawler
        $c = HtmlPageCrawler::create('<p>abc</p><p>def</p>');
        $this->assertEquals(2, $c->length);

        // not existing property throws exception
        try {
            $c->foo;
        } catch (\Exception $e) {
            $this->assertEquals('No such property foo', $e->getMessage());
            return;
        }
        $this->fail();
    }
}
