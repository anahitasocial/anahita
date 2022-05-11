<?php

/**
 * Set Photo Edge.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPhotosDomainEntityEdge extends ComBaseDomainEntityEdge
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'aliases' => array(
                'photo' => 'nodeA',
                'set' => 'nodeB',
            ),
            'attributes' => array(
                'ordering',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * After adding a relationship, set the photo count for the set;.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        $this->set->setValue('photo_count', $this->set->photos->reset()->getTotal());
    }

    /**
     * After deleting a relationship, set the photo count for the set;.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $total = $this->set->photos->reset()->getTotal();

        if ($total > 0) {
            $this->set->setValue('photo_count', $this->set->photos->reset()->getTotal());
        } else {
            $this->set->delete();
        }
    }

//end class
}
