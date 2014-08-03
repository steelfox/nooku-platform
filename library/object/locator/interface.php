<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Object  Locator Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Object
 */
interface ObjectLocatorInterface
{
    /**
     * Returns a fully qualified class name for a given identifier.
     *
     * @param ObjectIdentifier $identifier An identifier object
     * @param bool  $fallback   Use the fallback sequence to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(ObjectIdentifier $identifier, $fallback = true);

    /**
     * Find an identifier class
     *
     * @param array  $info      The class information
     * @param bool   $fallback  If TRUE use the fallback sequence
     * @return bool|mixed
     */
    public function find(array $info, $fallback = true);

    /**
     * Register a package
     *
     * @param  string $name    The package name
     * @param  string $domain  The domain for the package
     * @return ObjectLocatorInterface
     */
    public function registerPackage($name, $domain);

    /**
     * Get the registered package domain
     *
     * If no domain has been registered for this package, the default 'Nooku' domain will be returned.
     *
     * @param string $name The package name
     * @return string The domain
     */
    public function getPackage($name);

    /**
     * Get the registered packages
     *s
     * @return array An array with package names as keys and domain as values
     */
    public function getPackages();

    /**
     * Get the locator fallback sequence
     *
     * @return array
     */
    public function getSequence();

    /**
     * Get the locator type
     *
     * @return string
     */
    public function getType();
}