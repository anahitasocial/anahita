<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @copyright  Copyright (C) 2010 PeerGlobe Technology Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateFilterAlias extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterRead, LibBaseTemplateFilterWrite
{
    /**
     * The alias read map
     *
     * @var array
     */
    protected $_alias_read = array(
        '@helper('      => '$this->renderHelper(',
        '@service('     => '$this->getService(',
        '@date('        => '$this->renderHelper(\'date.format\',',
        '@overlay('     => '$this->renderHelper(\'behavior.overlay\', ',
        '@text('        => 'AnTranslator::_(',
        '@template('    => '$this->loadIdentifier(',
        '@route('       => '$this->getView()->getRoute(',
        '@escape('      => '$this->getView()->escape(',
        '@title(' => 'KService::get(\'anahita:document\')->setTitle(',
        '@description(' => 'KService::get(\'anahita:document\')->setDescription(',
        '@controller(' => '$this->renderHelper(\'controller.getController\',',
        '@view(' => '$this->renderHelper(\'controller.getView\',',
        '@previous(' => '$this->getHelper(\'previous\')->load(',
        '@template(' => '$this->getView()->load(',
        '@route(' => '$this->getView()->getRoute(',
        '@html(\'' => '$this->renderHelper(\'com:base.template.helper.html.',
    );
    
    /**
     * The alias write map
     *
     * @var array
     */
    protected $_alias_write = array();
    
    /**
     * Append an alias
     *
     * @param array     An array of aliases to be appended
     * @return LibBaseTemplateFilterAlias
     */
    public function append(array $alias, $mode = LibBaseTemplateFilter::MODE_READ)
    {
        if ($mode & LibBaseTemplateFilter::MODE_READ) {
            $this->_alias_read = array_merge($this->_alias_read, $alias);
        }

        if ($mode & LibBaseTemplateFilter::MODE_WRITE) {
            $this->_alias_write = array_merge($this->_alias_write, $alias);
        }

        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string
     * @return LibBaseTemplateFilterAlias
     */
    public function read(&$text)
    {
        $text = str_replace(
            array_keys($this->_alias_read),
            array_values($this->_alias_read),
            $text);

        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string
     * @return LibBaseTemplateFilterAlias
     */
    public function write(&$text)
    {
        $text = str_replace(
            array_keys($this->_alias_write),
            array_values($this->_alias_write),
            $text);

        return $this;
    }
}
