<?php

use ResultTable\Formatter\Image;
use ResultTable\Formatter\Email;
use ResultTable\Formatter\Url;
use ResultTable\Formatter\Size;

class FormatTest extends PHPUnit_Framework_TestCase {

    public function testGetOptions()
    {
        $img = new Image('test:image|width:50|height:50');

        $options = $img->getOptions();

        $this->assertTrue( array_key_exists('width', $options));
        $this->assertEquals(50,$options['width']);

        $this->assertTrue( array_key_exists('height', $options));
        $this->assertEquals(50,$options['height']);
    }

    public function testEmailFormat()
    {
        $email = new Email('test:email|subject:hi');
        $email->setValue('test@test.com');

        $formatted = $email->format();

        $this->assertTag([
            'tag'=>'a',
            'attributes'=>[
                'href'=>'mailto:test@test.com?subject=hi'
            ],
            'content'=>'test@test.com'
        ], $formatted);
    }

    public function testUrlFormat()
    {
        $email = new Url('test:url');
        $email->setValue('http://yahoo.com');

        $formatted = $email->format();

        $this->assertTag([
            'tag'=>'a',
            'attributes'=>[
                'href'=>'http://yahoo.com'
            ],
            'content'=>'http://yahoo.com'
        ], $formatted);
    }

    public function testImageFormat()
    {
        $img = new Image('test:image|width:50|height:50');
        $img->setValue('img.jpg');

        $formatted = $img->format();

        $this->assertTag([
            'tag'=>'img',
            'attributes'=>[
                'src'=>'img.jpg',
                'width'=>'50',
                'height'=>'50'
            ]
        ], $formatted);
    }

    public function testSizeFormat()
    {
        $size = new Size('test:size');
        $size->setValue('4');

        $format = $size->format();

        $this->assertEquals('4 bytes',$format);
    }
}