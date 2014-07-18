<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

return array(

    'priority' => Library\ObjectBootstrapper::PRIORITY_LOW,

    'aliases'  => array(
        'user.provider'  => 'com:users.user.provider',
    ),

    'identifiers' => array(

        'dispatcher' => array(
            'authenticators' => array('com:users.dispatcher.authenticator.cookie'),
        ),

        'user.session' => array(
            'handler' => 'database'
        ),

        'lib:user.session.handler.database'  => array(
            'table' => 'com:users.database.table.sessions'
        )
    )

);