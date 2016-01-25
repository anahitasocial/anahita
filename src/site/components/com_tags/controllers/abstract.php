<?php

/**
 * Abstract Tag Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComTagsControllerAbstract extends ComBaseControllerService
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'request' => array(
                'scope' => '',
                'sort' => 'top',
                'days' => KRequest::get('get.days', 'int', 7),
            ),
        ));

        parent::_initialize($config);

        JFactory::getLanguage()->load('com_tags');
    }

    /**
     * Read Service.
     *
     * @param KCommandContext $context
     */
    protected function _actionRead(KCommandContext $context)
    {
        $entity = parent::_actionRead($context);

        if (!empty($entity->tagables)) {

            if ($this->scope) {
                $entity->tagables->scope($this->scope);
            }

            if ($this->sort == 'top') {
                $entity->tagables->sortTop();
            } else {
                $entity->tagables->sortRecent();
            }

            $entity->tagables->limit($this->limit, $this->start);
        }

        //print str_replace('#_', 'jos', $entity->tagables->getQuery());

        return $entity;
    }

    /**
     * Browse Service.
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        if (!$context->query) {
            $context->query = $this->getRepository()->getQuery();
        }

        $query = $context->query;

        $name = $this->getIdentifier()->name;

        $query->select('COUNT(*) AS count')
        ->join('RIGHT', 'edges AS edge', $name.'.id = edge.node_a_id')
        ->order('count', 'DESC')
        ->limit($this->limit, $this->start);

        if ($this->sort == 'trending') {
            $now = new KDate();
            $query->where('edge.created_on', '>', $now->addDays(-(int) $this->days)->getDate());
        }

        return $this->getState()->setList($query->toEntityset())->getList();
    }

    /**
     * Set the default Actor View.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return ComActorsControllerDefault
     */
    public function setView($view)
    {
        parent::setView($view);

        if (!$this->_view instanceof ComBaseViewAbstract) {
            $name = KInflector::isPlural($this->view) ? 'tags' : 'tag';
            $defaults[] = 'ComTagsView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComTagsView'.ucfirst($name).ucfirst($this->_view->name);
            register_default(array('identifier' => $this->_view, 'default' => $defaults));
        }

        return $this;
    }
}
