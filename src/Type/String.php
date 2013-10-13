<?php

namespace PHPocket\Type;

/**
 * Java & c# style wrapper for PHP strings
 * Works only with MB-extension, because internal encoding is
 * UTF-8
 * This object is immutable
 *
 * @overhead 0/2
 * @package PHPocket\String
 */
class String implements CustomTypeInterface,\Countable
{
    const EMPTYSTRING = '';
    const INTERNAL_ENCODING = 'UTF-8';
    /**
     * Value of object
     * @var string
     */
    protected $_string;

    /**
     * @var int
     */
    private $_charsLength;

    /**
     * @var int
     */
    private $_bytesLength;

    /**
     * Constructs a String object
     *
     * @param string $initial
     * @param null   $encoding
     * @throws \Exception
     */
    public function __construct( $initial = '', $encoding = null )
    {
        if (!function_exists('mb_convert_encoding')) {
            throw new \Exception('Multibyte extension is required');
        }
        if (empty($initial)) {
            $this->_string = self::EMPTYSTRING;
        } else if ($initial instanceof String) {
            // Cloning
            $this->_string = $initial->_string;
        } else if (
            !empty($encoding)
            && \strtoupper(\trim(\str_replace('-', '', $encoding))) != 'UTF8'
        ) {
            // Converting to UTF8
            $this->_string = \mb_convert_encoding($initial, 'UTF-8', $encoding);
        } else {
            $this->_string = (string) $initial;
        }

        $this->_bytesLength = \strlen($this->_string);
        $this->_charsLength = \mb_strlen(
            $this->_string,
            self::INTERNAL_ENCODING
        );
    }

    /**
     * Returns true if provided string (object or system type) is null
     * or contains whitespace characters only
     *
     * @param string|self $string string to examine
     *
     * @return bool
     */
    static public function isNullOrWhitespace($string)
    {
        if ($string === null) {
            return true;
        }
        //Implicit cast
        $string = (string) $string;
        if (\strlen($string) === 0 || \strlen(\trim($string)) === 0) {
            return true;
        }
        // String is not empty
        return false;
    }

    /**
     * Returns true if provided string (object or system type) is null
     * or zero-length. This function does not trim string!!!
     *
     * @param string|self $string string to examine
     *
     * @return bool
     */
    static public function isNullOrEmpty($string)
    {
        if ($string === null) {
            return true;
        }
        // Implicit cast
        $string = (string) $string;
        return \strlen($string) === 0;
    }

    /**
     * Wraps array of string, returns an array of String objects
     *
     * @param string[] $array
     * @return self[]
     */
    static public function wrap($array)
    {
        if ($array === null) {
            return array();
        }
        $array = (array) $array;
        $answer = array();
        foreach ($array as $row) {
            if ($row instanceof String) {
                $answer[] = $row;
            } else {
                $answer[] = new self($row);
            }
        }
        return $answer;
    }


    /**
     * Returns amount of bytes in string
     *
     * @return int
     */
    public function byteSize()
    {
        return $this->_bytesLength;
    }

    /**
     * Returns char at specified position or empty string
     *
     * @param int $index
     * @return string
     */
    public function charAt($index)
    {
        $char = $this->substring($index, 1);
        if ($char->isEmpty()) {
            return self::EMPTYSTRING;
        }
        return $char->_string;
    }

    /**
     * Binary safe string comparison
     *
     * @param string|self $string
     * @return int
     */
    public function compareTo($string)
    {
        if ($string === null) {
            return -1;
        }

        $string = new self($string);
        return \strcmp($this->_string, $string->_string);
    }

    /**
     * Binary-comparing to string, but lowecase them
     *
     * @param string|self $string
     * @return int
     */
    public function compareToIgnoreCase($string)
    {
        if ($string === null) {
            return -1;
        }

        $string = new self($string);
        return \strcmp(
            $this->toLowerCase()->_string,
            $string->toLowerCase()->_string
        );
    }

    /**
     * Concatenates strings
     *
     * @param string|self $string
     * @return self
     */
    public function concat($string)
    {
        if ($string === null) {
            return $this;
        }
        $string = new self($string);
        if ($string->isEmpty()) {
            return $this;
        }
        return new self($this->_string . $string->_string);
    }


    /**
     * Returns true if internal string contains pattern
     *
     * @param string|self $pattern
     * @return bool
     */
    public function contains($pattern)
    {
        return $this->indexOf($pattern) > -1;
    }

    /**
     * Alias for @see{$this->length()}
     * Returns amount of chars in string
     *
     * @return int
     */
    public function count()
    {
        return $this->length();
    }

    /**
     * Returns true if internal string ends with provided
     * pattern
     *
     * @param string|self $pattern
     * @return bool
     */
    public function endsWith($pattern)
    {
        $pattern = new self($pattern);
        if ($this->length() < $pattern->length()) {
            // Pattern is bigger than internal string
            return false;
        }

        return $this->substring(-$pattern->length())->equals($pattern);
    }

    /**
     * Returns true if two string are binary-equal
     *
     * @param string|self $string
     * @return bool
     */
    public function equals($string)
    {
        if ($string === null) {
            return false;
        }

        $string = new self($string);
        return $this->_string === $string->_string;
    }

    /**
     * Returns true if this string equals provided
     * Both strings forced as lowercase
     *
     * @param string|self $string
     * @return bool
     */
    public function equalsIgnoreCase($string)
    {
        if ($string === null) {
            return false;
        }
        $string = new self($string);

        return
            $this->toLowerCase()->_string === $string->toLowerCase()->_string;
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     * Returns -1 if provided pattern not found
     *
     * @param string $pattern
     * @param int    $fromIndex
     * @return int
     */
    public function indexOf($pattern, $fromIndex = 0)
    {
        $value = \strpos($this->_string, $pattern, $fromIndex);
        $value = ($value === false) ? -1 : $value;
        return $value;
    }

    /**
     * Returns this only if length of string equals zero
     * Use @see{$this->isEmptyOrWhiteSpace} method if you
     * want to check a whitespaces
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->_charsLength === 0;
    }

    /**
     * Returns true if string is empty or contains whitespace
     * characters only
     *
     * @return bool
     */
    public function isEmptyOrWhiteSpace()
    {
        return $this->_charsLength == 0 || trim($this->_string) == '';
    }

    /**
     * Find the position of the last occurrence of a substring in a string
     *
     * @param string $pattern
     * @param int    $fromIndex
     * @return int
     */
    public function lastIndexOf($pattern, $fromIndex = 0)
    {
        return \strrpos($this->_string, $pattern, $fromIndex);
    }

    /**
     * Returns amount of chars in string
     *
     * @return int
     */
    public function length()
    {
        return $this->_charsLength;
    }

    /**
     * Returns true if internal string matches provided
     * regular expression
     *
     * @param string $regex Regular expression
     * @return bool
     */
    public function matches($regex)
    {
        return \preg_match($regex, $this->_string) === 1;
    }

    /**
     * Replaces substring and returns a new object
     *
     * @param string|string[] $old
     * @param string|string[] $new
     * @return self
     */
    public function replace($old,$new)
    {
        if ($this->isEmpty()) {
            return $this;
        }
        return new self(\str_replace($old, $new, $this->_string), null);
    }

    /**
     * Splits a String to a parts by regex and then returns array of
     * results in string or String[] format depending on value
     * of $wrapToObject argument
     *
     * @param string   $regex        Regular expression
     * @param int|null $limit        Limit, or (null|0|-1) to no limit
     * @param bool     $wrapToObject if false returns array of plain php strings
     *
     * @return string[]|self[]
     */
    public function split($regex, $limit = null, $wrapToObject = true)
    {
        if ($this->isEmpty()) {
            return array();
        }

        $array = \preg_split($regex, $this->_string, $limit);
        if (\count($array) > 0 && $wrapToObject) {
            return self::wrap($array);
        }
        return $array;
    }

    /**
     * Explodes a string by separator and then returns array of
     * results in string or String[] format depending on value
     * of $wrapToObject argument
     *
     * @param string $char         char to explode by
     * @param bool   $wrapToObject if false returns array of plain php strings
     * @return array|self
     * @throws \InvalidArgumentException
     */
    public function explode($char, $wrapToObject = true)
    {
        if ($char === null || $char == '') {
            throw new \InvalidArgumentException('Empty delimiter');
        }
        if ($this->isEmpty()) {
            return array();
        }

        $array = \explode($char, $this->_string);
        if ($wrapToObject) {
            return self::wrap($array);
        }
        return $array;
    }

    /**
     * Returns true is internal string begins with pattern
     *
     * @param string|self $pattern
     * @return bool
     */
    public function startsWith($pattern)
    {
        return $this->indexOf($pattern, 0) === 0;
    }

    /**
     * Returns a substring
     *
     * @param int $start
     * @param int $length
     * @return self
     */
    public function substring($start, $length = 0)
    {
        if ($this->isEmpty()) {
            // Inner string is empty
            return new self(self::EMPTYSTRING, null);
        }
        if ($start < 0) {
            $start += $this->length();
        }
        if ($start >= $this->length()) {
            // Start is bigger then inner string length
            return new self(self::EMPTYSTRING, null);
        }
        if ($length < 1 || ($start + $length) > $this->length()) {
            // Calculating length of substring
            $length = $this->length() - $start;
        }
        // Maybe self
        if ($start == 0 && $length == $this->length()) {
            return $this;
        }

        // Returning substring
        return new self(
            \mb_substr(
                $this->_string,
                $start,
                $length,
                self::INTERNAL_ENCODING
            ),
            null);
    }


    /**
     * Returns lower-case version of String
     *
     * @return self
     */
    public function toLowerCase()
    {
        if ( $this->isEmpty() ) {
            return $this;
        }
        return new self(\mb_strtolower($this->_string, self::INTERNAL_ENCODING));
    }

    /**
     * Returns upper-case version of string
     *
     * @return self
     */
    public function toUpperCase()
    {
        if ( $this->isEmpty() ) {
            return $this;
        }
        return new self(\mb_strtoupper($this->_string, self::INTERNAL_ENCODING));
    }

    /**
     * Returns trimmed version of String object
     *
     * @return self
     */
    public function trim()
    {
        if ( $this->isEmpty() ) {
            return $this;
        }
        return new self(\trim($this->_string), null);
    }


    /**
     * Returns a value of string object
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_string;
    }

    // MAGIC methods
    /**
     * PHP's magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}