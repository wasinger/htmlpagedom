<?php
namespace Wa72\HtmlPageDom\Tests;

use Wa72\HtmlPageDom\HtmlPageCrawler;

class HtmlPageCrawlerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::__construct
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::filter
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::getFirstNode
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::nodeName
     */
    public function testHtmlPageCrawler() {
        $c = new HtmlPageCrawler;
        $c->addHtmlContent('<html><body><div id="content"><h1>Title</h1></div></body></html>');
        $title = $c->filter('#content > h1');
        
        $this->assertInstanceOf('\Wa72\HtmlPageDom\HtmlPageCrawler', $title);
        $this->assertInstanceOf('\DOMNode', $title->getFirstNode());
        $this->assertEquals('h1', $title->nodeName());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::append
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::getInnerHtml
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setInnerHtml
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::prepend
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::makeEmpty
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setAttribute
     */
    public function testManipulationFunctions() {
        $c = new HtmlPageCrawler;
        $c->addHtmlContent('<html><body><div id="content"><h1>Title</h1></div></body></html>');
        
        $content = $c->filter('#content');
        $content->append('<p>Das ist ein Testabsatz');
        
        $this->assertEquals('<h1>Title</h1><p>Das ist ein Testabsatz</p>', $content->getInnerHtml());
        
        $content->setInnerHtml('<p>Ein neuer <b>Inhalt</p>');
        $this->assertEquals('<p>Ein neuer <b>Inhalt</b></p>', $content->getInnerHtml());
        
        $content->prepend('<h1>Neue Überschrift');
        $this->assertEquals('<h1>Neue &Uuml;berschrift</h1><p>Ein neuer <b>Inhalt</b></p>', $content->getInnerHtml());

        $h1 = $content->filter('h1');
        $this->assertEquals('Neue Überschrift', $h1->text());

        $b = $content->filter('b');
        $this->assertEquals('Inhalt', $b->text());

        $b2 = $c->filter('#content p b');
        $this->assertEquals('Inhalt', $b2->text());

        $content->append('<p class="a2">Zweiter Absatz</p>');
        $content->append('<p class="a3"><b>Dritter Absatz</b> und noch mehr Text</p>');

        $a3 = $content->filter('p.a3');
        $this->assertEquals('<b>Dritter Absatz</b> und noch mehr Text', $a3->getInnerHtml());

        $a3b = $a3->filter('b');
        $this->assertEquals('Dritter Absatz', $a3b->text());

        $body = $c->filter('body');
        $this->assertEquals('<div id="content"><h1>Neue &Uuml;berschrift</h1><p>Ein neuer <b>Inhalt</b></p><p class="a2">Zweiter Absatz</p><p class="a3"><b>Dritter Absatz</b> und noch mehr Text</p></div>', $body->getInnerHtml());

        $body->makeEmpty();
        $this->assertEmpty($body->getInnerHtml());

        $body->setAttribute('class', 'mybodyclass');
        $this->assertEquals('mybodyclass', $body->attr('class'));

    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::isHtmlDocument
     */
    public function testIsHtmlDocument() {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="content"><h1>Title</h1></div></body></html>');
        $c = new HtmlPageCrawler($dom);

        $this->assertTrue($c->isHtmlDocument());

        $t = $c->filter('body');
        $this->assertFalse($t->isHtmlDocument());

    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::saveHTML
     */
    public function testSaveHTML() {
        $html = "<!DOCTYPE html>\n<html><body><h1>Title</h1><p>Paragraph 1</p><p>Paragraph 2</p></body></html>\n";
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        $c = new HtmlPageCrawler($dom);
        $this->assertEquals($html, $c->saveHTML());
        $ps = $c->filter('p');
        $this->assertEquals('<p>Paragraph 1</p><p>Paragraph 2</p>', $ps->saveHTML());
        $t = $c->filter('h1');
        $this->assertEquals('<h1>Title</h1>', $t->saveHTML());
    }

    /**
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::css
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::getStyle
     * @covers Wa72\HtmlPageDom\HtmlPageCrawler::setStyle
     */
    public function testCss() {
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
    public function testClasses() {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="content"><h1>Title</h1></div></body></html>');
        $c = new HtmlPageCrawler($dom);
        $t = $c->filter('h1');
        $t->addClass('ueberschrift');
        $t->addClass('nochneklasse');
        $this->assertEquals('<h1 class="ueberschrift nochneklasse">Title</h1>', $t->saveHTML());
        $this->assertTrue($t->hasClass('ueberschrift'));
        $this->assertTrue($t->hasClass('nochneklasse'));
        $t->removeClass('nochneklasse');
        $this->assertTrue($t->hasClass('ueberschrift'));
        $this->assertFalse($t->hasClass('nochneklasse'));
    }

}
