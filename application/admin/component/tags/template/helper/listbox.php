<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Listbox Template Helper
 *
 * @author  Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @package Component\Tags
 */
class TagsTemplateHelperListbox extends Library\TemplateHelperListbox
{
    public function tags($config = array())
    {
    	$config = new Library\ObjectConfig($config);
    	$config->append(array(
    		'model'  => 'tags',
    		'value'	 => 'id',
    		'label'	 => 'title',
            'prompt' => false,
            'creatable' => false,
            'selected' => array(),
            'filter' => array()
        ));
        
        $config->label = 'title';
		$config->sort  = 'title';

        if($config->creatable)
        {
	        return $this->_renderCreateableTags($config);
        } else {
            return parent::_render($config);
        }
    }

    /**
    +	 * Renders the creatable tags input & js
    +	 *
    +	 * @param Library\ObjectConfig $config
    +	 * @return string
    +	 */
	protected function _renderCreateableTags(Library\ObjectConfig $config)
	{
		$tags = $this->getObject('com:tags.model.tags')->setState(Library\ObjectConfig::unbox($config->filter))->getRowset();

		$data = array();
		foreach($tags AS $tag){
			$data[] = array('id' => $tag->id, 'text' => $tag->title);
		}

		$attribs = $this->buildAttributes($config->attribs);

		return '<input type="text" name="'.$config->name.'" '.$attribs.' value="'.implode(',', Library\ObjectConfig::unbox($config->selected)).'" />
		<script data-inline>
            ;(function($){
	            $(".select-tags").select2({
		            createSearchChoice:function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
		            multiple: true,
		            data: '.json_encode($data).'
	            });
            })($jQuery)
        </script>';
    }
}