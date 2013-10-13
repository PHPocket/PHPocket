<?php

namespace PHPocket\Serialize;

/**
 * JSON serializer
 *
 * @package PHPocket\Serialize
 */
class JSON implements SerializerInterface
{

    protected $_useUTFCleaner;

    /**
     * Constructor
     *
     * @param bool $cleanUTF if true, UTF8 chars ( like cyrillic ), will be
     * converted from \uXXXX form into normal chars
     *
     * @throws \Exception if php5-json not installed
     */
    public function __construct($cleanUTF = true)
    {
        $this->_useUTFCleaner = (bool) $cleanUTF;
        if (!function_exists('json_encode')) {
            throw new \Exception(
                'JSON php extension not installed'
            );
        }
    }

    /**
     * Cleans up UTF8 characters
     *
     * @param string $string
     *
     * @return string
     */
    protected function _cleanUTF8Chars($string)
    {
        if (!$this->_useUTFCleaner) return $string;

        return preg_replace_callback(
            '/((\\\u[01-9a-fA-F]{4})+)/',
            function($m){
                return json_decode('"' . $m[1] .  '"');
            },
            $string
        );
    }

    /**
     * Serializes $data into the string
     *
     * @param mixed $data
     * @return string
     */
    public function pack($data)
    {
        return $this->_cleanUTF8Chars(json_encode($data));
    }

    /**
     * Serializes $data, but threats it as array
     *
     * @param array $data
     * @return mixed
     */
    public function packArray($data)
    {
        if ($data === null) {
            $data = array();
        }
        $data = array_values($data);
        return $this->_cleanUTF8Chars(json_encode($data));
    }

    /**
     * Deserializes $data and returns data
     *
     * @param string $data
     * @return mixed
     */
    public function unpack($data)
    {
        if (empty($data)) return null;
        return json_decode($data);
    }

    /**
     * Deserializes $data and tries to provide
     * array as result
     *
     * @param string $data
     * @return array|mixed
     */
    public function unpackArray($data)
    {
        if (empty($data)) return null;
        return json_decode($data, true);
    }

}