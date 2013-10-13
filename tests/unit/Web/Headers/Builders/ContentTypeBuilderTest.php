<?php

namespace PHPocket\Tests\Web\Headers\Builders;


use PHPocket\Web\Headers\Builders\ContentTypeBuilder;

class ContentTypeBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testExceptFiles()
    {
        $this->assertSame('Content-Type: text/plain; charset=utf-8', (string) ContentTypeBuilder::getText());
        $this->assertSame('Content-Type: text/html; charset=utf-8',  (string) ContentTypeBuilder::getHTML());
        $this->assertSame('Content-Type: image/png',  (string) ContentTypeBuilder::getImagePng());
        $this->assertSame('Content-Type: image/gif',  (string) ContentTypeBuilder::getImageGif());
        $this->assertSame('Content-Type: image/jpeg',  (string) ContentTypeBuilder::getImageJpg());
        $this->assertSame('Content-Type: application/json; charset=utf-8',  (string) ContentTypeBuilder::getJSON());
        $this->assertSame('Content-Type: application/octet-stream',  (string) ContentTypeBuilder::getBinary());
        $this->assertSame('Content-Type: application/pdf',  (string) ContentTypeBuilder::getPDF());

        $this->assertSame('Content-Type: application/octet-stream', (string) ContentTypeBuilder::findForFilename('x'));
    }

    public function testMSFormats()
    {
        // MS formats
        // http://technet.microsoft.com/en-us/library/ee309278%28office.12%29.aspx

        $this->assertSame(
            'application/vnd.ms-word',
            ContentTypeBuilder::findForExt('doc')->getValue()
        );
        $this->assertSame(
            'application/vnd.ms-excel',
            ContentTypeBuilder::findForExt('xls')->getValue()
        );
        $this->assertSame(
            'application/vnd.ms-powerpoint',
            ContentTypeBuilder::findForExt('ppt')->getValue()
        );

        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ContentTypeBuilder::findForExt('docx')->getValue()
        );
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ContentTypeBuilder::findForExt('xlsx')->getValue()
        );
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ContentTypeBuilder::findForExt('pptx')->getValue()
        );
    }
}