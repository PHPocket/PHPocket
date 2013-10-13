<?php
namespace PHPocket\Web;

/**
 * Library of HTTP status codes
 *
 * @package PHPocket\Web
 */
class HTTPStatusCodes
{
    /**
     * Returns full string for HTTP header
     *
     * @param int $code HTTP status code
     * @return string
     * @throws \InvalidArgumentException if no entry for provided code found
     */
    static public function getFullText($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Code is empty');
        }

        if (!isset(self::$list[$code])) {
            throw new \InvalidArgumentException(
                "Unknown code '{$code}'"
            );
        }

        return 'HTTP/'
            . ( in_array($code, self::$httpVersionOne) ? '1.0' : '1.1' )
            . ' ' . $code . ' '
            . self::$list[$code];
    }


    /**
     * Current default HTTP version
     *
     * @var string
     */
    public static $httpVersionDefault = '1.1';

    /**
     * List of HTTP 1.0 status codes
     *
     * @var int[]
     */
    public static $httpVersionOne = array(
        200, 201, 202, 203,
        300, 301, 302, 303, 304,
        400, 401, 403, 404,
        500, 501, 502, 503
    );

    /**
     * List of HTTP Status codes with text
     *
     * @var string[]
     */
    public static $list = array(
        // Continuing process
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // WebDAV; RFC 2518

        // Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // WebDAV; RFC 4918
        208 => 'Already Reported', // WebDAV; RFC 5842
        226 => 'IM Used', // RFC 3229

        // Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        // Client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC 2324
        419 => 'Authentication Timeout',
        422 => 'Unprocessable Entity', // WebDAV; RFC 4918
        423 => 'Locked', // WebDAV; RFC 4918
        424 => 'Failed Dependency', // WebDAV; RFC 4918
        426 => 'Upgrade Required', // RFC 2817
        428 => 'Precondition Required', // RFC 6585
        429 => 'Too Many Requests', // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585

        // Server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC 2295
        507 => 'Insufficient Storage', // WebDAV; RFC 4918
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended', // RFC 2774
        511 => 'Network Authentication Required', // RFC 6585
        522 => 'Connection Timed Out',
    );

}