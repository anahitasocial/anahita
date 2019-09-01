<?php

/**
 * JSON View Class.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTagsViewTagJson extends ComBaseViewJson{
    
    /**
     * Return the Item.
     *
     * @return array
     */
    protected function _getItem()
    {
        $item = parent::_getItem();
        
        if ($entity = $this->_state->getItem()) {
            $data = [];
            $taggables = $entity->taggables;
            
            foreach ($taggables as $taggable) {
                $data[] = $this->_serializeToArray($taggable, null);
            }
    
            $item['taggables'] = array(
                'data' => $data,
                'pagination' => array(
                    'offset' => (int) $taggables->getOffset(),
                    'limit' => (int) $taggables->getLimit(),
                    'total' => (int) $taggables->getTotal(),
                ),
            );
        }
        
        return $item;
    }
}
