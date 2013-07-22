<?php
/**
* @version      $Id: alias.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Template
* @subpackage   Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.nooku.org
*/

/**
 * Template read filter for aliases such as @template, @text, @helper, @route etc
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Filter
 */
class KTemplateFilterAlias extends KTemplateFilterAbstract implements KTemplateFilterRead, KTemplateFilterWrite
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
        '@text('        => 'JText::_(',
        '@template('    => '$this->loadIdentifier(',
        '@route('       => '$this->getView()->getRoute(',
        '@escape('      => '$this->getView()->escape(',
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
     * @return KTemplateFilterAlias
     */
    public function append(array $alias, $mode = KTemplateFilter::MODE_READ)
    {
        if($mode & KTemplateFilter::MODE_READ) {
            $this->_alias_read = array_merge($this->_alias_read, $alias);
        }

        if($mode & KTemplateFilter::MODE_WRITE) {
            $this->_alias_write = array_merge($this->_alias_write, $alias);
        }

        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
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
     * @return KTemplateFilterAlias
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