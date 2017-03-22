<?php
namespace Wa72\HtmlPageDom\Tests;

use Wa72\HtmlPageDom\HtmlPage;

class HtmlPageTest extends \PHPUnit_Framework_TestCase
{
    public function testHtmlPage()
    {
        $hp = new HtmlPage;
        $this->assertEquals("<!DOCTYPE html>\n<html><head><title></title></head><body></body></html>\n", $hp->__toString());

        $title = 'Erste Testseite';
        $hp->setTitle($title);
        $this->assertEquals($title, $hp->getTitle());

        $title = 'Seite "schön & gut" >> so wird\'s, süß';
        $hp->setTitle($title);
        $this->assertEquals($title, $hp->getTitle());

        $description = 'Dies ist die erste "Testseite" >> so wird\'s, süß';
        $hp->setMeta('description', $description);
        $this->assertEquals($description, $hp->getMeta('description'));

        $hp->removeMeta('description');
        $this->assertNull($hp->getMeta('description'));

        $bodycontent = '<div id="content">Testcontent1</div>';
        $body = $hp->filter('body');
        $body->setInnerHtml($bodycontent);
        $this->assertEquals($bodycontent, $body->getInnerHtml());
        $this->assertEquals($bodycontent, $hp->filter('body')->getInnerHtml());

        $content = '<h1>Überschrift</h1><p>bla bla <br><b>fett</b></p>';
        $hp->setHtmlById('content', $content);
        // echo $hp;
        $this->assertEquals(mb_convert_encoding($content, 'HTML-ENTITIES', 'utf8'), $hp->getElementById('content')->getInnerHtml());

        $url = 'http://www.tuebingen.de/';
        $hp->setBaseHref($url);
        $this->assertEquals($url, $hp->getBaseHref());
    }


    public function testClone()
    {
        $hp = new HtmlPage;
        $this->assertEquals("<!DOCTYPE html>\n<html><head><title></title></head><body></body></html>\n", $hp->__toString());

        $title = 'Erste Testseite';
        $hp->setTitle($title);
        $this->assertEquals($title, $hp->getTitle());

        $hp2 = clone $hp;

        $newtitle = 'Seitentitel neu';
        $hp->setTitle($newtitle);

        $this->assertEquals($title, $hp2->getTitle());
        $this->assertEquals($newtitle, $hp->getTitle());
    }

    public function testScript()
    {
        $html =<<<END
<!DOCTYPE html>
<html>
<head>
<title></title>
<script>
// this will be awesome
alert('Hello world');
</script>
</head>
<body>
</body>
</html>

END;
        $hp = new HtmlPage($html);
        $hp->getBody()->append('<h1>Script Test</h1>');
        $newhtml = $hp->save();

        $expected =<<<END
<!DOCTYPE html>
<html><head><title></title><script>
// this will be awesome
alert('Hello world');
</script></head><body>
<h1>Script Test</h1></body></html>

END;
        $this->assertEquals($expected, $newhtml);

    }

    public function testMinify()
    {
        $html =<<<END
<!DOCTYPE html>
<html>
<head>
<title></title>
<script>
// this will be awesome
alert('Hello world');
</script>
</head>
<body>
    <h1>TEST</h1>
    <p class="">
    asdf jksdlf ajsfk
    <b>jasdf
    jaksfd asdf</b>
    <a>jasdf jaks</a>
    </p>
</body>
</html>

END;
        $hp = new HtmlPage($html);

        $expected = <<<END
<!DOCTYPE html>
<html><head><title></title><script>alert('Hello world');</script></head><body><h1>TEST</h1><p>asdf jksdlf ajsfk <b>jasdf jaksfd asdf</b> <a>jasdf jaks</a></p></body></html>

END;
        $this->assertEquals($expected, $hp->minify()->save());

    }

    public function testIndent()
    {
        $html =<<<END
<!DOCTYPE html>
<html>
<head>
<title></title>
<script>
// this will be awesome
alert('Hello world');
</script>
</head>
<body>
    <h1>TEST</h1>
    <p class="">
    asdf jksdlf ajsfk
    <b>jasdf
    jaksfd asdf</b>
    <a>jasdf jaks</a>
    </p>
</body>
</html>

END;
        $hp = new HtmlPage($html);

        $expected = <<<END
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<script>
// this will be awesome
alert('Hello world');
		</script>
	</head>
	<body>
		<h1>TEST</h1>
		<p class="">asdf jksdlf ajsfk <b>jasdf jaksfd asdf</b> <a>jasdf jaks</a></p>
	</body>
</html>

END;
        $this->assertEquals($expected, $hp->indent()->save());

    }
}
