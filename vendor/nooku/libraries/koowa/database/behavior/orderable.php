<?php
/**
 * @version 	$Id: orderable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Orderable Behavior
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehaviorOrderable extends KDatabaseBehaviorAbstract
{
	/**
	 * Get the methods that are available for mixin based
	 *
	 * This functions conditionaly mixes the behavior. Only if the mixer
	 * has a 'ordering' property the behavior will be mixed in.
	 *
	 * @param object The mixer requesting the mixable methods.
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();

		if(isset($mixer->ordering)) {
			$methods = parent::getMixableMethods($mixer);
		}

		return $methods;
	}

	/**
	 * Override to add a custom WHERE clause
	 *
	 * <code>
	 * 	   $query->where('category_id', '=', $this->id);
	 * </code>
	 *
	 * @param 	KDatabaseQuery $query
	 * @return  void
	 */
	public function _buildQueryWhere(KDatabaseQuery $query)
	{

	}

	/**
	 * Move the row up or down in the ordering
	 *
	 * Requires an 'ordering' column
	 *
	 * @param	integer	Amount to move up or down
	 * @return 	KDatabaseRowAbstract
	 * @throws 	KDatabaseBehaviorException
	 */
	public function order($change)
	{
		//force to integer
		settype($change, 'int');

		if($change !== 0)
		{
			$old = (int) $this->ordering;
			$new = $this->ordering + $change;
			$new = $new <= 0 ? 1 : $new;

			$table = $this->getTable();
			$db    = $table->getDatabase();
			$query = $db->getQuery();

			//Build the where query
			$this->_buildQueryWhere($query);

			$update =  'UPDATE `'.$db->getTableNeedle().$table->getBase().'` ';
			if($change < 0)
			{
				$update .= 'SET ordering = ordering+1 ';
				$query->where('ordering', '>=', $new)
					  ->where('ordering', '<', $old);
			}
			else
			{
				$update .= 'SET ordering = ordering-1 ';
				$query->where('ordering', '>', $old)
					  ->where('ordering', '<=', $new);
			}

			$update .= (string) $query;
			$db->execute($update);

			$this->ordering = $new;
			$this->save();
			$this->reorder();
		}

		return $this->_mixer;
	}

	 /**
     * Resets the order of all rows
     *
     * Resetting starts at $base to allow creating space in sequence for later
     * record insertion.
     *
     * @param	integer 	Order at which to start resetting.
     * @return	KDatabaseBehaviorOrderable
     */
    public function reorder($base = 0)
    {
		//force to integer
        settype($base, 'int');

        $table  = $this->getTable();
        $db     = $table->getDatabase();
        $query  = $db->getQuery();

        //Build the where query
        $this->_buildQueryWhere($query);

        if ($base)  {
            $query->where('ordering', '>=', (int) $base);
        }

        $db->execute("SET @order = $base");
        $db->execute(
             'UPDATE '.$db->getTableNeedle().$table->getBase().' '
            .'SET ordering = (@order := @order + 1) '
            .(string) $query.' '
            .'ORDER BY ordering ASC'
        );

        return $this;
    }

    /**
     * Find the maximum ordering within this parent
     *
     * @return int
     */
    protected function getMaxOrdering()
    {
        $table  = $this->getTable();
        $db     = $table->getDatabase();
        $query  = $db->getQuery();

        $this->_buildQueryWhere($query);

        $select = 'SELECT MAX(ordering) FROM `'.$db->getTableNeedle().$table->getName().'`';
        $select .= (string) $query;

        return  (int) $db->select($select, KDatabase::FETCH_FIELD);

    }

 	/**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties
     * with fresh data from the table on success.
     *
     * @return KDatabaseRowAbstract
     */
    protected function _beforeTableInsert(KCommandContext $context)
    {
        if(isset($this->ordering))
        {
            $max = $this->getMaxOrdering();

            if ($this->ordering <= 0) {
                $this->ordering = $max + 1;
            } else {
                $this->reorder($this->ordering);
            }
        }
    }

    /**
     * Changes the rows ordering if the virtual order field is set. Order is
     * relative to the row's current position.
     *
     * @param   KCommandContext Context
     */
    protected function _beforeTableUpdate(KCommandContext $context)
    {
        if(isset($this->order) && isset($this->ordering)) {
            $this->order($this->order);
        }
    }

    /**
     * Clean up the ordering after an item was deleted
     *
     * @param   KCommandContext Context
     */
    protected function _afterTableDelete(KCommandContext $context)
    {
        $this->reorder();
    }
}