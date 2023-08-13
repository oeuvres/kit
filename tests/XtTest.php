<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Oeuvres\Kit\Xt;

final class XtTest extends TestCase
{
    public function testReplaceText(): void
    {
        $xml = "<test>
    <p>This a <i>test</i></p>
    <p>This a <i>second test</i></p>
</test>
        ";
        $doc = Xt::loadXML($xml);
        $search =  '/test/';
        $replace = 'success';
        Xt::replaceText($doc, $search, $replace);
        $this->assertXmlStringEqualsXmlString("<?xml version=\"1.0\"?>
<test>
    <p>This a <i>success</i></p>
    <p>This a <i>second success</i></p>
</test>", $doc->saveXML());
    }
}
