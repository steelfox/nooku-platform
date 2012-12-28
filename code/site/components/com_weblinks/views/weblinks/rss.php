<?php
/**
 * @version		$Id$
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Weblink Rss View
 *
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Weblinks
 */
class ComWeblinksViewWeblinksRss extends KViewRss
{
	public function display()
    {
        //Get the category
        $category = $this->getCategory();

        $this->assign('category'  , $category);
    	return parent::display();
    }

    public function getCategory()
    {
        //Get the category
        $category = $this->getService('com://site/weblinks.model.categories')
                         ->table('weblinks')
                         ->id($this->getModel()->getState()->category)
                         ->getRow();

        //Set the category image
        if (isset( $category->image ) && !empty($category->image))
        {
            $path = JPATH_IMAGES.'/stories/'.$category->image;
            $size = getimagesize($path);

            $category->image = (object) array(
                'path'   => '/'.str_replace(JPATH_ROOT.DS, '', $path),
                'width'  => $size[0],
                'height' => $size[1]
            );
        }

        return $category;
    }
}