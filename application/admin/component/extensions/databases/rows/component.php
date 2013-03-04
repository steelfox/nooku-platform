<?php
/**
 * @package     Nooku_Server
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Component Database Row Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Extensions    
 */

class ComExtensionsDatabaseRowComponent extends KDatabaseRowTable
{
	/**
     * Whitelist for keys to get from the xml manifest
     *
     * @var array
     */
    protected static $_manifest_fields = array(
    	'creationDate',
        'author',
        'copyright',
        'authorEmail',
        'authorUrl',
        'version',
        'description'
    );
	
	/**
	 * Get a value by key
	 *
	 * This method is specialized because of the magic property "description"
	 * which reads from the plugin xml file
	 *
	 * @param   string  The key name.
	 * @return  string  The corresponding value.
	 */
	public function __get($column)
	{
	    if($column == 'title' && empty($this->_data['title'])) 
	    {
            if(!empty($this->manifest)) {
                $this->_data['title'] = $this->manifest->name;
            } else {
                 $this->_data['title'] = '';
            }
        }
	    
	    if($column == 'manifest' && empty($this->_data['manifest'])) 
		{
            $path = $this->getIdentifier()->getNamespace('admin');
            $file = $path.'/component/'.$this->option.'/manifest.xml';
              
            if(file_exists($file)) {
		        $this->_data['manifest'] = simplexml_load_file($file);
            } else {
                $this->_data['manifest'] = '';
            }
        }

		if(in_array($column, self::$_manifest_fields) && empty($this->_data[$column])) {
            $this->_data[$column] = isset($this->manifest->{$column}) ? $this->manifest->{$column} : '';
        }
        
	    if($column == 'params' && !($this->_data['params']) instanceof JParameter)
        {
            $path = $this->getIdentifier()->getNamespace('admin');
            $file = $path.'/component/'.$this->option.'/config.xml';

	        $this->_data['params'] = new JParameter( $this->_data['params'], $file, 'component' );
        }
        
		return parent::__get($column);
	}
	
	/**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        
        //Include the manifest fields
        foreach(self::$_manifest_fields as $field) {
           $data[$field] = (string) $this->$field;
        }
        
        $data['title']  = (string) $this->title;
        $data['params'] = $this->params->toArray();
        return $data;
    }
}