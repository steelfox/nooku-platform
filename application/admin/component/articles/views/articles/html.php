<?php
/**
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Articles HTML view class.
 *
 * @author     Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @category   Nooku
 * @package    Nooku_Server
 * @subpackage Articles
 */
class ArticlesViewArticlesHtml extends Framework\ViewHtml
{
    public function render()
    {        
        $state = $this->getModel()->getState();
        
        $parent_id = $this->getService('com:articles.model.categories')
                            ->table('articles')
                            ->id($state->category)
                            ->getRow()
                            ->parent_id;
        
        // Enable sortable
        $this->sortable = $parent_id && $state->sort == 'ordering' && $state->direction == 'asc';
        
        return parent::render();
    }
}