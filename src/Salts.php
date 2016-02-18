<?php

namespace WP_CLI_Dotenv_Command;

class Salts
{
    /**
     * WordPress.org Salt Generator Service URL
     */
    const GENERATOR_URL = 'https://api.wordpress.org/secret-key/1.1/salt/';

    /**
     * Pattern to match both key and value
     */
    const PATTERN_CAPTURE = '#\'([^\']+)\'#';

    /**
     * @return array|void
     * @throws \Exception
     */
    public static function fetch_array()
    {
        // read in each line as an array
        $response = file(static::GENERATOR_URL);

        if ( ! is_array($response)) {
            throw new \Exception('There was a problem fetching salts from the WordPress generator service.');
        }

        return (array)static::parse_php_to_array($response);
    }

    /**
     * Parse the php generated by the WordPress.org salts generator to an array of key => value pairs
     *
     * @param $response
     *
     * @return array
     */
    public static function parse_php_to_array(array $response)
    {
        $salts = [];

        array_map(function ($line) use (&$salts) {
            // capture everything between single quotes
            preg_match_all(self::PATTERN_CAPTURE, $line, $matches);
            // matches[x]
            //   0 - complete match
            //   1 - captures
            list($name, $value) = $matches[ 1 ];

            $salts[ $name ] = $value;
        }, $response);

        return $salts;
    }
}