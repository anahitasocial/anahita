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
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback(array(
            'after.delete',
            'after.add', ),
            array($this, 'redirect'));
    }

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
                'sort' => 'trending',
                'days' => KRequest::get('get.days', 'int', 7),
            ),
        ));

        parent::_initialize($config);

        $this->getService('anahita:language')->load('com_tags');
    }

    /**
     * Read Service.
     *
     * @param KCommandContext $context
     */
    protected function _actionRead(KCommandContext $context)
    {
        $entity = parent::_actionRead($context);

        $pkg = $this->getIdentifier()->package;
        $name = $this->getIdentifier()->name;

        $this->getToolbar('menubar')->setTitle(sprintf(AnTranslator::_('COM-'.$pkg.'-TERM'), $name));

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
        $entities = parent::_actionBrowse($context);

        if(in_array($this->sort, array('top', 'trending')) && $this->q == '') {

            $package = $this->getIdentifier()->package;
            $name = $this->getIdentifier()->name;

            $entities->select('COUNT(*) AS count')
            ->join('RIGHT', 'edges AS edge', $name.'.id = edge.node_a_id')
            ->where('edge.type', 'LIKE', '%com:'.$package.'.domain.entity.tag')->group($name.'.id')
            ->order('count', 'DESC');

            if ($this->sort == 'trending') {
                $now = new AnDate();
                $entities->where('edge.created_on', '>', $now->addDays(-(int) $this->days)->getDate());
            }
        }

        return $entities;
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
            $name = AnInflector::isPlural($this->view) ? 'tags' : 'tag';
            $defaults[] = 'ComTagsView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComTagsView'.ucfirst($name).ucfirst($this->_view->name);
            register_default(array('identifier' => $this->_view, 'default' => $defaults));
        }

        return $this;
    }

    /**
     * Set the necessary redirect.
     *
     * @param KCommandContext $context
     */
    public function redirect(KCommandContext $context)
    {
        $url = array();
        $url['view'] = AnInflector::pluralize($this->getIdentifier()->name);
        $url['option'] = $this->getIdentifier()->package;

        if ($context->action == 'add') {
            $url['id'] = $this->getItem()->id;
        }

        $this->getResponse()->setRedirect(route($url));
    }
}
