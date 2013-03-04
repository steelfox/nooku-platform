<?php
/**
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template Decorator Filter
 *
 * Replace <ktml:content /> with the view contents allowing to the template to act as a view decorator.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterDecorator extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
	 * Replace <ktml:content /> with the view contents
	 *
	 * @param string
	 * @return KTemplateFilterDecorator
	 */
	public function write(&$text)
	{
        $matches = array();
        if(preg_match_all('#<ktml:content(.*)\/>#iU', $text, $matches))
        {
            foreach($matches[1] as $key => $match) {
                $text = str_replace($matches[0][$key], $this->getTemplate()->getView()->getContent(), $text);
            }
        }

		return $this;
	}
}
