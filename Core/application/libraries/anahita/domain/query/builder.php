<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Query Builder
 * 
 * It Constructs different parts of a query and join them together as a string 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainQueryBuilder extends KObject
{	
	/**
	 * Singleton Instance
	 * 
	 * @var AnDomainQueryBuilder
	 */
	static protected $_instance;
	
	/**
	 * Return an instance of a builder
	 * 
	 * @return AnDomainQueryBuilder
	 */
	static public function getInstance()
	{		
		if ( !self::$_instance ) 
		{
			self::$_instance = new AnDomainQueryBuilder(new KConfig());
		}
					
		return self::$_instance;
	}
	
	/**
	 * Database Adapter
	 * 
	 * @var KDatabaseAbstract
	 */
	protected $_store;
	
	/**
	 * Current Query that's being built
	 * 
	 * @return AnDomainQuery
	 */
	protected $_query;

	/**
	 * Builds an update query
	 *
	 * @param AnDomainQuery $query Query object
	 * 
	 * @return string
	 */
	public function update($query)
	{
		$resources = $query->getRepository()->getResources();
		
		foreach($resources as $resource) 
		{
			$names[] = $query->getPrefix().$resource->getName().' AS '.$this->_store->quoteName($resource->getAlias());
			//only join update if there are no order			
			if ( !empty($query->order) )
				break;
		}
		
		
		$operation = $query->operation['value'];
		
		if ( is_array($query->operation['value']) ) 
		{
			$operation = array();
			foreach($query->operation['value'] as $key => $value) 
			{
				$operation[] = is_numeric($key) ? $value : '@col('.$key.')='.$this->_store->quoteValue($value);
			}
			$operation = implode(',', $operation);
		}
		
		//only join update if there are no order
		if ( empty($query->order) )
		{
			$resources = $query->getRepository()->getResources();
					
			foreach($resources->getLinks() as $link)
			{
				$query->where($link->child,'=',$link->parent);
			}
		}
				
		$clause    = 'UPDATE '.implode($names,', ').' SET '.$operation;				
		return $clause;		
	}
	
	/**
	 * Builds an delete query. Handles multiple resources at once
	 *
	 * @param AnDomainQuery $query Query object
	 * 
	 * @return string
	 */
	public function delete($query)
	{
		$resources = $query->getRepository()->getResources();
				
		foreach($resources->getLinks() as $link) {
			$query->where($link->child,'=',$link->parent);
		}

		$names = array();
		
		foreach($resources as $resource) {
			$query->from($resource->getName().' AS '.$this->_store->quoteName($resource->getAlias()));
			$names[] = $this->_store->quoteName($resource->getAlias());
		}
				
		$clause = 'DELETE '.implode(', ', $names);
		
		return $clause;		
	}	
	
	/**
	 * Return the where clause of a query
	 * 
	 * @param AnDomainQuery $query Query object
	 * 
	 * @return string
	 */	
	public function select($query)
	{
 		$clause = 'SELECT @DISTINCT';

        if ( $query->operation['type'] & AnDomainQuery::QUERY_SELECT_DEFAULT )
        {
			$description = $query->getRepository()->getDescription();
			$columns	 = AnDomainQueryHelper::parseColumns($query, $query->columns);			
			foreach($description->getProperty() as $name => $property) 
			{
				if ( isset($columns[$name])  ) 
				{
					$result = AnDomainQueryHelper::parseColumn($query, $name);
					if ( $result['columns'] instanceof AnDomainResourceColumn ) 
					{
						$columns[] = $columns[$name].' AS '.$this->_store->quoteValue($result['columns']->key());
						unset($columns[$name]);	
					}
				}
				else if ( $property->isAttribute() ) 
				{
					$columns[] = $property->getColumn();
				} 
				elseif ( $property->isRelationship() && $property->isManyToOne() ) 
				{
					$columns  = array_merge($columns, array_values(KConfig::unbox($property->getColumns())));
				}
			}
						
			if ( $description->getInheritanceColumn() )
				$columns[] = $description->getInheritanceColumn();
							
        	foreach($columns as $key => $column) 
	    	{	    		
	    		if ( $column instanceof AnDomainResourceColumn  ) 
	    		{
	    			if ( $column->resource->getLink() ) {
	    				 $columns[$key] = $column.' AS '.$this->_store->quoteValue((string)$column);
	    				 continue;
	    			}
	    			else {	    			    
	    				$columns[$key] = (string)$column;
	    			}
	    		}
	    		if ( !is_numeric($key) ) {
	    			$columns[$key] = $column.' AS '.$this->_store->quoteValue($key);
	    		}
	    	}
			$clause .= ' '.implode(' , ', $columns);        	
        } else 
        {   
        	$columns = (array) $query->operation['value'];
        	$columns = AnDomainQueryHelper::parseColumns($query, $columns);
        	$clause .= ' '.implode(' , ', $columns);
        }
         
		return $clause;
	}	
	
	/**
	 * Return the where clause of a query
	 * 
	 * @return string
	 */	
	public function from($query)
	{
		$clause = '';
		
		$resource = $query->getRepository()->getResources()->main();
		
		$query->from($resource->getName().' AS '.$this->_store->quoteName($resource->getAlias())); 
				
	    if (!empty($query->from)) 
        {
            $clause = ' FROM '.implode(' , ', $query->from);
        }
        				
		return $clause;
	}
	
	/**
	 * Return the join clause
	 * 
	 * @return string
	 */	
	public function join($query)
	{
		$clause = '';
		$repository	    = $query->getRepository();
		$resources		= $repository->getResources();
		$links		    = $resources->getLinks();
		foreach($links as $link)
		{
		    $type = strtoupper($link->resource->getLinkType());
			switch($type)
			{
			    case 'STRONG' : $type = 'INNER';break;
			    case 'WEAK'   : $type = 'LEFT'; break;
			}
			$query->join($type, $link->resource->getName().' AS '.$this->_store->quoteName($link->resource->getAlias()),array(
				$link->child.'='.$link->parent
			));
		}
		
		$this->_links($query, $query->link);
		
		if (!empty($query->join))
		{
			$joins = array();
            foreach ($query->join as $join) 
            {
            $tmp = '';
                
            if (! empty($join['type'])) {
                    $tmp .= $join['type'] . ' ';
                }
               
                $tmp .= 'JOIN ' . $join['resource'];
                $tmp .= ' ON ' . implode(' AND ', $join['condition']);
           
                $joins[] = $tmp;
            }
	            
            $clause = implode(' ', $joins);            
		}

		return $clause;
	}
	
	/**
	 * Recursively adds a the links to the query
	 *
	 * @param AnDomainQuery $query Query object
	 * 
	 * @param array 
	 */
	protected function _links($query, $links)
	{		
		settype($links, 'array');
		
		foreach($links as $link)
		{
			$resource    = $link['resource'];
			$description = $link['query']->getRepository()->getDescription();			
			$conditions  = array();			
			
			foreach($link['conditions'] as $key => $value)
			{			    
				if ( is_numeric($key) )
					$conditions[] = $value;
				elseif ( $value instanceof AnDomainResourceColumn)
					$conditions[] = $key.' = '.(string)$value;
				else 
					$conditions[] = $key.' = '.$this->_store->quoteValue($value);
			}
			
			if ( $link['bind_type'] )
			{
			    $type = $this->_inheritanceTree($description);
			    if ( !empty($type) && $type != '%' ) {
			        $conditions[] = $link['bind_type'].' LIKE \''.$this->_inheritanceTree($description).'\'';
			    }
			}
			
			$name      = $this->_store->quoteName($link['resource_name']);
			$type      = strtoupper($link['type']);
			switch($type)
			{
			    case 'STRONG' : $type = 'INNER';break;
			    case 'WEAK'   : $type = 'LEFT';break;
			}
			//$name    = $this->_store->quoteName($resource->getName());
			$query->join($type, $resource->getName().' AS '.$name, $conditions);
			
			$this->_links($query, $link->query->link);
		}		
	}
	
	/**
	 * Return the where clause of a query
	 * 
	 * @return string
	 */	
	public function where($query)
	{
		$clauses = $this->_where($query);
				
		$clause  = implode(' AND ', $clauses);
		
		if ( !empty($clause) )
			$clause = 'WHERE '.$clause;
		
		return $clause;
	}
	
	/**
	 * Builds Query where clauses recursively
	 *
	 * @return array
	 */
	protected function _where($query)
	{
		$link 		= null;
		$type_check = true;
		
		if ( $query instanceof KConfig ) 
		{
			$type_check = false;		
			$query = $query->query;
		}
			
		$description = $query->getRepository()->getDescription();				
		$clauses	 = array();
		if ( $description->getInheritanceColumn() && $type_check ) 
		{
			$resource 	= $description->getInheritanceColumn()->resource->getAlias();
			//the table type column name
			$type_column_name = $resource.'.'.$description->getInheritanceColumn()->name;
								
			$scopes 	= KConfig::unbox($query->instance_of);
			if ( empty($scopes) ) {
				$scopes = array($description);
			} else {
				if ( !is_array($scopes) ) {
					$scopes = array($scopes);
				}
			}
            
			foreach($scopes as $index => $scope) {
			    $inheritance_type = $this->_inheritanceTree($scope);
			    if ( !empty($inheritance_type) && $inheritance_type != '%' ) {
                    $scopes[$index] = $type_column_name.' LIKE \''.$inheritance_type.'\'';;
			    } else {
			        unset($scopes[$index]);
			    }
			}
			if ( !empty($scopes) ) {
                $clauses[] = '('.implode(' OR ', $scopes).')';
			}
		}
		
		if ( !empty($query->where) )
		{
			$clause = '';
            foreach($query->where as $where) 
            	$clause .= $this->_buildWhere($query, $where);
            if ( !empty($clause) )
            	$clauses[] = '('.$clause.')';
		}
		
		foreach($query->link as $link) 
		{
			$clauses = array_merge($clauses, $this->_where($link));
		}
		
		return $clauses;		
	}
	
	/**
	 * Builds WHERE part of a query
	 *
	 * @param AnDomainQuery $query  Query object
	 * @param array         $where  Where conditions
	 * 
	 * @return string
	 */
	protected function _buildWhere($query, $where)
	{
		$clause = '';
		
		if(isset($where['condition'])) {
			$clause .= ' '.$where['condition'];
		}

		//converts subclause to string only if there's at least one where statement in it
		if ( isset($where['clause']) ) 
		{
			if ( count($where['clause']) == 0 )
				return '';
				
			$clause .= ' (';
			foreach($where['clause'] as $subwhere) {
				$clause .= $this->_buildWhere($query, $subwhere);
			}
			$clause .= ' )';
			return $clause;
		}
					
		list($columns, $property)  = array_values(AnDomainQueryHelper::parseColumn($query, $where['property']));

		$value 			   =  isset($where['value']) ?  $where['value'] : null;
		$where['property'] = pick($columns, $where['property']);

		if ( $property && !$value instanceof AnDomainQuery )
		{
			$constraint = $where['constraint'];
			
			if ( is_object($value) || is_array($columns) )
			{
				if ( $value instanceof KObjectSet || is_array($value) )
				{
					$values    = $value;
					$keys	   = array();
					$clauses   = array();
					foreach($values as $value) 
					{						
						$columns = $property->serialize($value);
						foreach($columns as $column => $value) {
							$keys[$column][] = $value;
						}
					}
					
					foreach($keys as $key => $values ) {
						$clauses[] = $this->_buildWhere($query, array('property'=>$key,'constraint'=>$constraint, 'value'=>$values));
					}
					
					$clause .= ' ('.implode(' AND ', $clauses).')';
					return $clause;
				}
				else 
				{
					$columns   = $property->serialize($value);
					
					$clauses   = array();
					foreach($columns as $column => $value) 
					{
						$clauses[] = $this->_buildWhere($query, array('property'=>$column,'constraint'=>$constraint, 'value'=>$value));
					}
					
					$clause .= ' ('.implode(' AND ', $clauses).')';
					
					return $clause;					
				}
			}
		} 
		
		$clause  .= ' '.$where['property'];
		
		if(isset($where['constraint'])) 
		{
			$value    = $this->_store->quoteValue($where['value']);

			if ( $value === 'NULL' ) {
				//force correct constraint
				if ( strpos($where['constraint'], 'IS') === false )
				{
					if ( strpos($where['constraint'], '<>') !== false )
						 $where['constraint'] = ' IS NOT ';
					elseif ( strpos($where['constraint'], '=') !== false )
						 $where['constraint'] = ' IS ';					
				}
			}
			
			if(in_array($where['constraint'], array('IN', 'NOT IN'))) {
				$value = ' ( '.$value. ' ) ';
			}

			$clause .= ' '.$where['constraint'].' '.$value;
		}
		
		return $clause;	
	}	
		
	/**
	 * Return the group clause
	 * 
	 * @return string
	 */	
	public function group($query)
	{
		$clause = '';
		
		if (!empty($query->group)) 
		{
			$columns = AnDomainQueryHelper::parseColumns($query, $query->group);
			$clause = ' GROUP BY '.implode(' , ', $columns);
		}
		
		return $clause;		
	}
	
	/**
	 * Return the having clause
	 * 
	 * @return string
	 */	
	public function having($query)
	{
		$clause = '';
		
		if (!empty($query->having)) 
		{
			$clause = ' HAVING '.implode(' , ', $query->having);
		}
		
		return $clause;		
	}	
	
	/**
	 * Return the order clause
	 * 
	 * @return string
	 */	
	public function order($query)
	{
		$clause = '';
		
		if (!empty($query->order) ) 
		{
			$clause = 'ORDER BY ';
			$list = array();
            foreach ($query->order as $order) {
            	$columns = AnDomainQueryHelper::parseColumns($query, $order['column']);
            	foreach($columns as $column)
            		$list[]  = $column.' '.$order['direction'];
            }
            
            $clause .= implode(' , ', $list);
		}

		return $clause;
	}
	
	/**
	 * Return the limit clause
	 * 
	 * @return string
	 */	
	public function limit($query)
	{
		$clause = '';
		
		if (!empty($query->limit)) 
		{
		    switch($query->operation['type'])
		    {
			    case AnDomainQuery::QUERY_SELECT_DEFAULT :
			    case AnDomainQuery::QUERY_SELECT  :
			        $clause = ' LIMIT '.$query->offset.' , '.$query->limit;
			        break;
			    case AnDomainQuery::QUERY_UPDATE :
			        if ( (int) $query->limit > 0 )
			            $clause = ' LIMIT '.$query->limit;
			        break;
		    }
		}

		return $clause;
	}	
		
	/**
	 * Builds a query into a final query statement
	 * 
	 * @param AnDomainQuery $query  Query object
	 * 
	 * @return string
	 */
	public function build($query)
	{
		//clone the query so it won't be modified
		$query   = clone $query;
		$parts   = array();
		$this->_query     = $query;
		$this->_store   = $query->getRepository()->getStore();
		switch($query->operation['type'])
		{
			case AnDomainQuery::QUERY_SELECT_DEFAULT :
			case AnDomainQuery::QUERY_SELECT  :
				$parts[] =	$this->select($query);
				$parts[] =	$this->from($query);
				$parts[] =  '@MYSQL_JOIN_PLACEHOLDER';
				$parts[] =	$this->where($query);
				$parts[] =	$this->group($query);
				$parts[] =	$this->having($query);
				$parts[] =	$this->order($query);
				$parts[] =	$this->limit($query);
				break;
			case AnDomainQuery::QUERY_UPDATE :
				$parts[] = $this->update($query);
				$parts[] = $this->where($query);
				$parts[] = $this->order($query);
				$parts[] = $this->limit($query);								
				break;
			case AnDomainQuery::QUERY_DELETE :
				$parts[] = $this->delete($query);
				$parts[] = $this->from($query);				
				$parts[] = $this->where($query);
				break;	
			default : 
				_die();		
		}
				
		$string = implode(' ', $parts);		
		$string = $this->parseMethods($query, $string);
		$string = str_replace('@MYSQL_JOIN_PLACEHOLDER', $this->join($query), $string);
		$string = $this->parseMethods($query, $string);

		if ( count($query->binds) ) {
			foreach($query->binds as $key => $value) {
				$key    = ':'.$key;
				$value  = $this->_store->quoteValue($value);
				$string = str_replace($key, $value, $string);
			}
		}
		
        $string = str_replace('@DISTINCT', $query->distinct ? 'DISTINCT' : '', $string);
				
		return 	$string;
	}
	
	/**
	 * Creates a string representaiton of class hiearchy of an entity in format of
	 * Parent Class 1,Parent Class 2,Parent Class n,Entity Class
	 *
	 * @param AnDomainDescriptionAbstract|string $description Entity Description or Class name
	 * 
	 * @return string
	 */
	protected function _inheritanceTree($description)
	{
        $inheritance = '';
        
	    if ( $description instanceof KServiceIdentifier || 
	    		(is_string($description) && strpos($description, '.') && !strpos($description, ',')) 	    		
	    		) {
	        $description = KService::get($description)->getRepository()->getDescription();
	    }
	    
        else if ( $description instanceof AnDomainRepositoryAbstract ) {
        	$description = $description->getDescription();
        }
         	    
        if ( $description instanceof AnDomainDescriptionAbstract ) 
        {
            $inheritance = (string) $description->getInheritanceColumnValue();
            
            if ( $description->isAbstract() ) {
                $inheritance .= '%';
            }
            
        } 
        
        elseif ( is_string($description) ) {
            $inheritance = strpos($description,'.') ? $description : $description.'%';
        }        
        
        return $inheritance;
	}
	
	/**
	 * Builds a query into a final query statement
	 * 
	 * @param AnDomainQuery $query  Query object
	 * @param string        $string A String object
	 * 
	 * @return string
	 */
	public function parseMethods($query, $string)
	{
	    //replaces any @col(\w+) pattern with the correct column name
	    if ( strpos($string,'@col(') )
	    {
	        $matches = array();
	        if  (preg_match_all('/@col\((.*?)\)/', $string, $matches))
	        {
	            $description = $query->getRepository()->getDescription();
	            $replaces = array();
	            foreach($matches[1] as $match) {
	                $result  	 = AnDomainQueryHelper::parseColumn($query, $match);
	                if ( empty($result['columns']) ) {
	                    $replaces[] = $match;
	                } else
	                    $replaces[] = (string) $result['columns'];
	            }
	    
	            $string = str_replace($matches[0], $replaces, $string);
	        }
	    }
	    
	    if ( strpos($string,'@quote(') )
	    {
	        $matches  = array();
	        $replaces = array();
	        if  (preg_match_all('/@quote\((.*?)\)/', $string, $matches))
	        {
	            foreach($matches[1] as $match) {
	                $replaces[] = $this->_store->quoteValue($match);
	            }
	            $string = str_replace($matches[0], $replaces, $string);
	        }
	    }
	    
	    if ( strpos($string,'@instanceof(') )
	    {
	        $matches  = array();
	        $replaces = array();
	        if  (preg_match_all('/\!?@instanceof\((.*?)\)/', $string, $matches))
	        {
	            foreach($matches[1] as $i => $match)
	            {
	                $operand = '';
	                if ( $matches[0][$i][0] == '!' ) {
	                    $operand = 'NOT ';
	                }
	                $type_col   = $query->getRepository()->getDescription()->getInheritanceColumn();
	                $classes    = explode(',', $match);
	                $statements = array();
	                foreach($classes as $class)
	                {
	                    $class        = $this->_store->quoteValue($class);
	                    $statements[] = $operand."FIND_IN_SET($class,$type_col)";
	                }
	                if ( $operand == 'NOT ' ) $operand = ' AND '; else $operand = ' OR ';
	                if ( count($statements) == 1 )
	                    $statements = implode($operand, $statements);
	                else
	                    $statements = '('.implode($operand, $statements).')';
	    
	                $replaces[] = $statements;
	            }
	            $string = str_replace($matches[0], $replaces, $string);
	        }
	    }
        
        if ( strpos($string,'@remove_from_set(') )
        {           
            $matches  = array();
            $replaces = array();
            if  (preg_match_all('/@remove_from_set\((.*?)\)/', $string, $matches))
            {
                foreach($matches[1] as $i => $match)
                {
                    list($set, $item) = explode(',', $match);
                    $set  = trim($set);
                    $item = trim($item);
                    $replaces[] = "TRIM(BOTH ',' FROM REPLACE(concat(',',$set,','),CONCAT(',',$item,','),','))";
                }
                
                $string = str_replace($matches[0],$replaces,$string);                
                
            }
        }
        
        if ( strpos($string,'@set_length(') )
        {
            $matches  = array();
            $replaces = array();
            if  (preg_match_all('/@set_length\((.*?)\)/', $string, $matches))
            {                
                foreach($matches[1] as $i => $match)
                {
                    $replaces[] = "LENGTH($match) - LENGTH(REPLACE($match, ',', '')) + 1";        
                }
                $string = str_replace($matches[0],$replaces,$string);
            }
        }        
                        
	    return $string;	    
	}
}