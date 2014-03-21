<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

define('NOOKU_PATH', dirname(__FILE__) . '/../..');

ini_set('xdebug.max_nesting_level', 2000);

$old = error_reporting();
error_reporting($old & ~E_STRICT);

// Boot Framework.
require_once '../../library/nooku.php';
\Nooku::getInstance();

$manager = Library\ObjectManager::getInstance();

spl_autoload_register(function ($class)
{
    $parts = explode(' ', strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class)));

    if (array_shift($parts) != 'nooku\script\translations') return;

    $file = dirname(__FILE__). '/'.implode('/', $parts) . '.php';

    if (file_exists($file))
    {
        require_once($file);
    }
});