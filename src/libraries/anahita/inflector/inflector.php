<?php
/**
 * @package     Anahita_Inflector
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */

/**
 * AnInflector to pluralize and singularize English nouns.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @author		Rastin Mehr <rastin@anahitapolis.com>
 * @package		Anahita_Inflector
 * @static
 */
class AnInflector
{
    /**
     * Rules for pluralizing and singularizing of nouns.
     *
     * @var array
     */
    protected static $_rules = array(
        'pluralization' => array(
            '/move$/i' => 'moves',
            '/sex$/i' => 'sexes',
            '/child$/i' => 'children',
            '/man$/i' => 'men',
            '/foot$/i' => 'feet',
            '/person$/i' => 'people',
            '/taxon$/i' => 'taxa',
            '/(quiz)$/i' => '$1zes',
            '/^(ox)$/i' => '$1en',
            '/(m|l)ouse$/i' => '$1ice',
            '/(matr|vert|ind|suff)ix|ex$/i'=> '$1ices',
            '/(x|ch|ss|sh)$/i' => '$1es',
            '/([^aeiouy]|qu)y$/i' => '$1ies',
            '/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
            '/sis$/i' => 'ses',
            '/([ti]|addend)um$/i' => '$1a',
            '/(alumn|formul)a$/i' => '$1ae',
            '/(buffal|tomat|her)o$/i' => '$1oes',
            '/(bu)s$/i' => '$1ses',
            '/(alias|status)$/i' => '$1es',
            '/(octop|vir)us$/i' => '$1i',
            '/(gen)us$/i' => '$1era',
            '/(ax|test)is$/i' => '$1es',
            '/s$/i' => 's',
            '/$/' => 's',
        ),

        'singularization' => array(
            '/cookies$/i' => 'cookie',
            '/moves$/i' => 'move',
            '/sexes$/i' => 'sex',
            '/children$/i' => 'child',
            '/men$/i' => 'man',
            '/feet$/i' => 'foot',
            '/people$/i' => 'person',
            '/taxa$/i' => 'taxon',
            '/databases$/i' => 'database',
            '/(quiz)zes$/i' => '\1',
            '/(matr|suff)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias|status)es$/i' => '\1',
            '/(alias|status)$/i' => '\1',
            '/(tomato|hero|buffalo)es$/i'  => '\1',
            '/([octop|vir])i$/i' => '\1us',
            '/(gen)era$/i' => '\1us',
            '/(cris|^ax|test)es$/i' => '\1is',
            '/(shoe)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/(bus)es$/i' => '\1',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1ovie',
            '/(s)eries$/i' => '\1eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/([^f])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti]|addend)a$/i' => '\1um',
            '/(alumn|formul)ae$/i' => '$1a',
            '/(n)ews$/i' => '\1ews',
            '/(.*)ss$/i' => '\1ss',
            '/(.*)s$/i' => '\1',
        ),
        
        'verbalization' => array(
            '/y$/i' => 'ied',
            '/(e)$/i' => '$1d',
            '/$/' => 'ed',
        ),

        'countable' => array(
            'aircraft',
            'cannon',
            'deer',
			'elk',
            'equipment',
            'fish',
            'information',
            'money',
            'moose',
			'rice',
			'salmon',
			'shrimp',
			'squid',
            'series',
            'sheep',
            'species',
            'swine',
			'trout',
			'tuna',
        )
    );

    /**
     * Cache of pluralized, singularized and verbalized nouns.
     *
     * @var array
     */
    protected static $_cache = array(
        'singularized' => array(),
        'pluralized' => array(),
        'verbalized' => array()
    );

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     */
    private function __construct() {}

    /**
     * Add a word to the cache, useful to make exceptions or to add words in
     * other languages
     *
     * @param	string	Singular word
     * @param 	string	Plural word
     * @param 	string	Verbal word
     */
    public static function addWord($singular, $plural, $verbal = null)
    {
        self::$_cache['pluralized'][$singular] = $plural;
        self::$_cache['singularized'][$plural] = $singular;
        
        self::$_cache['singularized'][$singular] = $singular;
        self::$_cache['pluralized'][$plural] = $plural;
        
        if (isset($verbal)) {
            self::$_cache['verbalized'][$singular] = $verbal;
            self::$_cache['verbalized'][$plura] = $verbal;
        }
    }

    /**
     * Singular English word to plural.
     *
     * @param 	string Word to pluralize
     * @return 	string Plural noun
     */
    public static function pluralize($word)
    {
        //Make sure we have the singular
        $word = self::singularize($word);
        
        //Get the cached noun of it exists
        if (isset(self::$_cache['pluralized'][$word])) {
            return self::$_cache['pluralized'][$word];
        }

        //Create the plural noun
        if (in_array($word, self::$_rules['countable'])) {
            self::$_cache['pluralized'][$word] = $word;
            return $word;
        }

        foreach (self::$_rules['pluralization'] as $regexp => $replacement) {
            $matches = null;
            $plural = preg_replace($regexp, $replacement, $word, -1, $matches);
            if ($matches > 0) {
                self::$_cache['pluralized'][$word] = $plural;
                return $plural;
            }
        }

        return $word;
    }

    /**
     * Plural English word to singular.
     *
     * @param 	string Word to singularize.
     * @return 	string Singular noun
     */
    public static function singularize($word)
    {
        //Get the cached noun of it exists
        if (isset(self::$_cache['singularized'][$word])) {
            return self::$_cache['singularized'][$word];
        }

        //Create the singular noun
        if (in_array($word, self::$_rules['countable'])) {
            self::$_cache['singularized'][$word] = $word;
            return $word;
        }


        foreach (self::$_rules['singularization'] as $regexp => $replacement) {
            $matches = null;
            $singular = preg_replace($regexp, $replacement, $word, -1, $matches);
            
            if ($matches > 0) {
                self::$_cache['singularized'][$word] = $singular;
                return $singular;
            }
        }

        return $word;
    }
    
    /**
     * Present English verb conjugated to preterite participle.
     *
     * @param 	string Word to verbalize.
     * @return 	string Present verb
     */
    public static function verbalize($word)
    {
        //Get the cached noun of it exists
        if (isset(self::$_cache['verbalized'][$word])) {
            return self::$_cache['verbalized'][$word];
        }
 
        foreach (self::$_rules['verbalization'] as $regexp => $replacement) {
            $matches = null;
            $verbal = preg_replace($regexp, $replacement, $word, -1, $matches);
            
            if ($matches > 0) {
                self::$_cache['verbalized'][$word] = $verbal;
                return $verbal;
            }
        }

        return $word;
    }

    /**
     * Returns given word as CamelCased
     *
     * Converts a word like "foo_bar" or "foo bar" to "FooBar". It
     * will remove non alphanumeric characters from the word, so
     * "who's online" will be converted to "WhoSOnline"
     *
     * @param   string 	$word    Word to convert to camel case
     * @return	string	UpperCamelCasedWord
     */
    public static function camelize($word)
    {
        $word = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $word);
        $word = str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $word))));
        
        return $word;
    }

    /**
     * Converts a word "into_it_s_underscored_version"
     *
     * Convert any "CamelCased" or "ordinary Word" into an "underscored_word".
     *
     * @param    string    $word    Word to underscore
     * @return string Underscored word
     */
    public static function underscore($word)
    {
        $word = preg_replace('/(\s)+/', '_', $word);
        $word = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
        
        return $word;
    }

    /**
     * Convert any "CamelCased" word into an array of strings
     *
     * Returns an array of strings each of which is a substring of string formed
     * by splitting it at the camelcased letters.
     *
     * @param	string  Word to explode
     * @return 	array	Array of strings
     */
    public static function explode($word)
    {
        $result = explode('_', self::underscore($word));
        return $result;
    }

    /**
     * Convert  an array of strings into a "CamelCased" word
     *
     * @param  array    $words   Array to implode
     * @return string  UpperCamelCasedWord
     */
    public static function implode($words)
    {
        $result = self::camelize(implode('_', $words));
        return $result;
    }

    /**
     * Returns a human-readable string from $word
     *
     * Returns a human-readable string from $word, by replacing
     * underscores with a space, and by upper-casing the initial
     * character by default.
     *
     * @param    string    $word    String to "humanize"
     * @return string Human-readable word
     */
    public static function humanize($word)
    {
        $result = ucwords(strtolower(str_replace("_", " ", $word)));
        return $result;
    }

    /**
     * Converts a class name to its table name according to Anahita
     * naming conventions.
     *
     * Converts "Person" to "people"
     *
     * @param  string    $className    Class name for getting related table_name.
     * @return string plural_table_name
     * @see classify
     */
    public static function tableize($className)
    {
        $result = self::underscore($className);

        if (! self::isPlural($className)) {
            $result = self::pluralize($result);
        }
        
        return $result;
    }

    /**
     * Converts a table name to its class name according to Anahita
     * naming conventions.
     *
     * Converts "people" to "Person"
     *
     * @see tableize
     * @param    string    $table_name    Table name for getting related ClassName.
     * @return string SingularClassName
     */
    public static function classify($tableName)
    {
        $result = self::camelize(self::singularize($tableName));
        return $result;
    }

    /**
     * Returns camelBacked version of a string.
     *
     * Same as camelize but first char is lowercased
     *
     * @param string $string
     * @return string
     * @see camelize
     */
    public static function variablize($string)
    {
        $string = self::camelize(self::underscore($string));
        $result = strtolower(substr($string, 0, 1));
        $variable = preg_replace('/\\w/', $result, $string, 1);
        
        return $variable;
    }

    /**
     * Check to see if an English word is singular
     *
     * @param string $string The word to check
     * @return boolean
     */
    public static function isSingular($string)
    {
        // Check cache assuming the string is plural.
        $singular = isset(self::$_cache['singularized'][$string]) ? self::$_cache['singularized'][$string] : null;
        $plural = $singular && isset(self::$_cache['pluralized'][$singular]) ? self::$_cache['pluralized'][$singular] : null;
        
        if ($singular && $plural) {
            return $plural != $string;
        }
        
        // If string is not in the cache, try to pluralize and singularize it.
        return self::singularize(self::pluralize($string)) == $string;
    }

    /**
     * Check to see if an Enlish word is plural
     *
     * @param string $string
     * @return boolean
     */
    public static function isPlural($string)
    {
        // Check cache assuming the string is singular.
        $plural   = isset(self::$_cache['pluralized'][$string]) ? self::$_cache['pluralized'][$string] : null;
        $singular = $plural && isset(self::$_cache['singularized'][$plural]) ? self::$_cache['singularized'][$plural] : null;
        
        if ($plural && $singular) {
            return $singular != $string;
        }
        
        // If string is not in the cache, try to singularize and pluralize it.
        return self::pluralize(self::singularize($string)) == $string;
    }

    /**
     * Gets a part of a CamelCased word by index
     *
     * Use a negative index to start at the last part of the word (-1 is the
     * last part)
     *
     * @param   string  Word
     * @param   integer Index of the part
     * @param   string  Default value
     */
    public static function getPart($string, $index, $default = null)
    {
        $parts = self::explode($string);

        if ($index < 0) {
            $index = count($parts) + $index;
        }

        return isset($parts[$index]) ? $parts[$index] : $default;
    }
}
