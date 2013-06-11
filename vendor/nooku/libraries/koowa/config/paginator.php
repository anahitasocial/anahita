<?php
/**
 * @version		$Id: paginator.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Config
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Paginator Config Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Config
 */
class KConfigPaginator extends KConfig
{
 	/**
     * Set a configuration element
     *
     * @param  string 
     * @param  mixed 
     * @return void
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
        
        //Only calculate the limit and offset if we have a total
        if($this->total)
        {
            $this->limit  = (int) max($this->limit, 1);
            $this->offset = (int) max($this->offset, 0);
        
            if($this->limit > $this->total) {
                $this->offset = 0;
            }
           
            if(!$this->limit) 
            {
                $this->offset = 0;
                $this->limit  = $this->total;
            }

            $this->count  = (int) ceil($this->total / $this->limit);

            if($this->offset > $this->total) {
                $this->offset = ($this->count-1) * $this->limit;
            }

            $this->current = (int) floor($this->offset / $this->limit) + 1;
        }
    }
    
 	/**
     * Implements lazy loading of the pages config property.
     *
     * @param string 
     * @return mixed
     */
    public function __get($name)
    {
        if($name == 'pages' && !isset($this->pages)) {
            $this->pages = $this->_pages();
        }
        
        return $this->get($name);
    }
   
 	/**
     * Get a list of pages
     *
     * @return  array   Returns and array of pages information
     */
    protected function _pages()
    {
        $pages = new KConfig();
        $current  = ($this->current - 1) * $this->limit;
        
        // First
        $page    = 1;
        $offset  = 0;
        $active  = $offset != $this->offset;
        
        $pages->first = array('title' => 'First', 'page' => 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active, 'rel' => 'first' );
      
        // Previous
        $offset  = max(0, ($this->current - 2) * $this->limit);
        $active  = $offset != $this->offset;
        $pages->prev = array('title' => 'Prev', 'page' => $this->current - 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active, 'rel' => 'prev');

        // Pages
        $offsets = array();
        foreach($this->_offsets() as $page => $offset)
        {
            $current = $offset == $this->offset;
            $offsets[] = array('title' => $page, 'page' => $page, 'offset' => $offset, 'limit' => $this->limit, 'current' => $current, 'active' => !$current);
        }
        
        $pages->offsets = $offsets;
        
        // Next
        $offset = min(($this->count-1) * $this->limit, ($this->current) * $this->limit);
        $active = $offset != $this->offset;
        $pages->next = array('title' => 'Next', 'page' => $this->current + 1, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active, 'rel' => 'next');
       
        // Last
        $offset  = ($this->count - 1) * $this->limit;
        $active  = $offset != $this->offset;
        $pages->last = array('title' => 'Last', 'page' => $this->count, 'offset' => $offset, 'limit' => $this->limit, 'current' => false, 'active' => $active, 'rel' => 'last');
        
        return $pages;
    }
    
    /**
     * Get the offset for each page, optionally with a range
     *
     * @return  array   Page number => offset
     */
    protected function _offsets()
    {
        if($display = $this->display)
        {
            $start  = (int) max($this->current - $display, 1);
            $start  = min($this->count, $start);
            $stop   = (int) min($this->current + $display, $this->count);
        }
        else // show all pages
        {
            $start = 1;
            $stop = $this->count;
        }

        $result = array();
        if($start > 0)
        {
            foreach(range($start, $stop) as $pagenumber) {
                $result[$pagenumber] =  ($pagenumber-1) * $this->limit;
            }
        }

        return $result;
    }
}