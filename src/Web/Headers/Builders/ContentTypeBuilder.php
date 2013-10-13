<?php
namespace PHPocket\Web\Headers\Builders;


use PHPocket\Web\Headers\ContentTypeHeader;
use PHPocket\Web\MimeTypes;

/**
 * Utility class for building common headers
 */
class ContentTypeBuilder
{
    /**
     * Returns a header for JSON content-type
     *
     * @param string $charset
     * @return ContentTypeHeader
     */
    static public function getJSON($charset = 'utf-8')
    {
        return new ContentTypeHeader(MimeTypes::findForExt('json'), $charset);
    }

    /**
     * Returns a header for plain text
     *
     * @param string $charset
     * @return ContentTypeHeader
     */
    static public function getText($charset = 'utf-8')
    {
        return new ContentTypeHeader(MimeTypes::findForExt('txt'), $charset);
    }

    /**
     * Return a header for html
     *
     * @param string $charset
     * @return ContentTypeHeader
     */
    static public function getHTML($charset = 'utf-8')
    {
        return new ContentTypeHeader(MimeTypes::findForExt('html'), $charset);
    }

    /**
     * Return a header for png images
     *
     * @return ContentTypeHeader
     */
    static public function getImagePng()
    {
        return new ContentTypeHeader(MimeTypes::findForExt('png'), null);
    }

    /**
     * Return a header for jpeg images
     *
     * @return ContentTypeHeader
     */
    static public function getImageJpg()
    {
        return new ContentTypeHeader(MimeTypes::findForExt('jpeg'), null);
    }

    /**
     * Return a header for gif images
     *
     * @return ContentTypeHeader
     */
    static public function getImageGif()
    {
        return new ContentTypeHeader(MimeTypes::findForExt('gif'), null);
    }

    /**
     * Return a header for PDF documents
     *
     * @return ContentTypeHeader
     */
    static public function getPDF()
    {
        return new ContentTypeHeader(MimeTypes::findForExt('pdf'), null);
    }

    /**
     * Return a header for binary data (octet-stream)
     *
     * @return ContentTypeHeader
     */
    static public function getBinary()
    {
        return new ContentTypeHeader(MimeTypes::$octet, null);
    }

    /**
     * Finds content type for provided file
     *
     * @param $filename
     * @return ContentTypeHeader
     * @throws \InvalidArgumentException
     */
    static public function findForFilename($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename is empty');
        }

        $chunks = explode('.', $filename);
        if (count($chunks) == 1) {
            // No extension in file
            return self::getBinary();
        }

        return self::findForExt($chunks[count($chunks) - 1]);
    }

     /**
     * Tries to find best content type for provided file extension
     * If not found return octet-stream
     *
     * @param string      $filenameExtension
     * @param string|null $charset
      *
     * @return ContentTypeHeader
     */
    static public function findForExt($filenameExtension, $charset = 'utf-8')
    {
        if (empty($filenameExtension)) {
            return self::getBinary();
        }

        return new ContentTypeHeader(
            MimeTypes::findForExt($filenameExtension),
            (MimeTypes::isTextExt($filenameExtension) ? $charset : null)
        );
    }
}