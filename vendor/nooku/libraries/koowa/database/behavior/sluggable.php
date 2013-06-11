<?php
/**
 * @version     $Id: abstract.php 1528 2010-01-26 23:14:08Z johan $
 * @package     Koowa_Database
 * @subpackage  Behavior
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Sluggable Behavior
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Behavior
 */
class KDatabaseBehaviorSluggable extends KDatabaseBehaviorAbstract
{
    /**
     * The column name from where to generate the slug, or a set of column
     * names to concatenate for generating the slug. Default is 'title'.
     *
     * @var array
     */
    protected $_columns;

    /**
     * Separator character / string to use for replacing non alphabetic
     * characters in generated slug. Default is '-'.
     *
     * @var string
     */
    protected $_separator;

    /**
     * Maximum length the generated slug can have. If this is null the length of
     * the slug column will be used. Default is NULL.
     *
     * @var integer
     */
    protected $_length;

    /**
     * Set to true if slugs should be re-generated when updating an existing
     * row. Default is true.
     *
     * @var boolean
     */
    protected $_updatable;
    
    /**
     * Set to true if slugs should be unique. If false and the slug column has
     * a unique index set this will result in an error being throw that needs
     * to be recovered. Default is NULL.
     *
     * @var boolean
     */
    protected $_unique;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null)
    {
        parent::__construct($config);

        foreach($config as $key => $value) 
        {
            if(property_exists($this, '_'.$key)) { 
                $this->{'_'.$key} = $value;
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'columns'   => array('title'),
            'separator' => '-',
            'updatable' => true,
            'length'    => null,
            'unique'    => null
        ));

        parent::_initialize($config);
    }

    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer
     * has a 'slug' property the behavior will be mixed in.
     *
     * @param object The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = array();
        
        if(isset($mixer->slug)) {
            $methods = parent::getMixableMethods($mixer);
        }

        return $methods;
    }
    
    /**
     * Insert a slug
     *
     * If multiple columns are set they will be concatenated and separated by the
     * separator in the order they are defined.
     *
     * Requires a 'slug' column
     *
     * @return void
     */
    protected function _afterTableInsert(KCommandContext $context)
    {
        if($this->_createSlug()) {
            $this->save();
        }
    }

    /**
     * Update the slug
     *
     * Only works if {@link $updatable} property is TRUE. If the slug is empty
     * the slug will be regenerated. If the slug has been modified it will be
     * sanitized.
     *
     * Requires a 'slug' column
     *
     * @return void
     */
    protected function _beforeTableUpdate(KCommandContext $context)
    {
        if($this->_updatable) {
            $this->_createSlug();
        }
    }

    /**
     * Create a sluggable filter
     *
     * @return void
     */
    protected function _createFilter()
    {
        $config = array();
        $config['separator'] = $this->_separator;

        if(!isset($this->_length)) {
            $config['length'] = $this->getTable()->getColumn('slug')->length;
        } else {
            $config['length'] = $this->_length;
        }
        
        //Create the filter
        $filter = $this->getService('koowa:filter.slug', $config);
        return $filter;
    }
    
    /**
     * Create the slug
     *
     * @return boolean  Return TRUE if the slug was created or updated successfully, otherwise FALSE.
     */
    protected function _createSlug()
    {
        //Create the slug filter
        $filter = $this->_createFilter();

        if(empty($this->slug))
        {
            $slugs = array();
            foreach($this->_columns as $column) {
                $slugs[] = $filter->sanitize($this->$column);
            }

            $this->slug = implode($this->_separator, array_filter($slugs));
            $this->_canonicalizeSlug();
            return true;
        }
        else
        {
            if(in_array('slug', $this->getModified())) 
            {
                $this->slug = $filter->sanitize($this->slug);
                $this->_canonicalizeSlug();
                return true;
            }
        }

        return false;
    }
    
    /**
     * Make sure the slug is unique
     * 
     * This function checks if the slug already exists and if so appends
     * a number to the slug to make it unique. The slug will get the form
     * of slug-x.
     *
     * @return void
     */
    protected function _canonicalizeSlug()
    {
        $table = $this->getTable();
        
        //If unique is not set, use the column metadata
        if(is_null($this->_unique)) { 
            $this->_unique = $table->getColumn('slug', true)->unique;
        }
    
        //If the slug needs to be unique and it already exist make it unqiue
        if($this->_unique && $table->count(array('slug' => $this->slug))) 
        {   
            $db    = $table->getDatabase();
            $query = $db->getQuery()
                        ->select('slug')
                        ->where('slug', 'LIKE', $this->slug.'-%');          
            
            $slugs = $table->select($query, KDatabase::FETCH_FIELD_LIST);
            
            $i = 1;
            while(in_array($this->slug.'-'.$i, $slugs)) {
                $i++;
            }
            
            $this->slug = $this->slug.'-'.$i;
        }
    }
}