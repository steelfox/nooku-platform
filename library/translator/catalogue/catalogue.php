<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Translator Catalogue
 *
 * @author  Arunas Mazeika <https://github.com/arunasmazeika>
 * @package Nooku\Library\Translator
 */
class TranslatorCatalogue extends ObjectArray implements TranslatorCatalogueInterface
{
    /**
     * Constructor.
     *
     * @param ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_sources = array();
    }

    /**
     * Load translations into the catalogue.
     *
     * @param array  $translations Associative array containing translations.
     * @param bool   $override     Whether or not existing translations can be overridden during import.
     * @return bool True on success, false otherwise.
     */
    public function load(array $translations, $override = false)
    {
        if ($override) {
            $this->_data = array_merge($this->_data, $translations);
        } else {
            $this->_data = array_merge($translations, $this->_data);
        }

        return true;
    }

    /**
     * Get a string from the registry
     *
     * @param  string $string
     * @return  string  The translation of the string
     */
    public function get($string)
    {
        return $this->offsetGet($string);
    }

    /**
     * Set a string in the registry
     *
     * @param  string $string
     * @param  string $translation
     * @return TranslatorCatalogue
     */
    public function set($string, $translation)
    {
        $this->offsetSet($string, $translation);
        return $this;
    }

    /**
     * Check if a string exists in the registry
     *
     * @param  string $string
     * @return boolean
     */
    public function has($string)
    {
        return $this->offsetExists((string) $string);
    }

    /**
     * Remove a string from the registry
     *
     * @param  string $string
     * @return TranslatorCatalogue
     */
    public function remove($string)
    {
        $this->offsetUnset($string);
        return $this;
    }

    /**
     * Clears out all strings from the registry
     *
     * @return  TranslatorCatalogue
     */
    public function clear()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Get a list of all strings in the catalogue
     *
     * @return  array
     */
    public function getStrings()
    {
        return array_keys($this->_data);
    }
}
