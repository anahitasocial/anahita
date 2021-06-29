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
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'request' => array(
                'scope' => '',
                'sort' => 'trending',
                'days' => AnRequest::get('get.days', 'int', 7),
            ),
        ));

        parent::_initialize($config);
        
        $this->getService('anahita:language')->load('com_tags');
    }

    /**
     * Read Service.
     *
     * @param AnCommandContext $context
     */
    protected function _actionRead(AnCommandContext $context)
    {
        $pkg = $this->getIdentifier()->package;
        $name = $this->getIdentifier()->name;
        
        if ($entity = parent::_actionRead($context)) {
            $entity->taggables->getRepository()->addBehavior('com:tags.domain.behavior.privatable');
            
            if ($this->scope) {
                $entity->taggables->scope($this->scope);
            }
            
            $alias = $entity
            ->taggables
            ->getRepository()
            ->getResources()
            ->main()
            ->getAlias();
            
            if ($this->sort == 'top') {
                $conditions = '(COALESCE(:alias.comment_count,0) + COALESCE(:alias.vote_up_count,0) + COALESCE(:alias.subscriber_count,0) + COALESCE(:alias.follower_count,0))';
                $conditions = str_replace(':alias', $alias, $conditions);
                $entity->taggables->order($conditions, 'DESC')->groupby('@col(taggable.id)');
            } else {
                $entity->taggables->order($alias.'.created_on', 'DESC');
            }
            
            $entity->taggables->limit($this->limit, $this->start);

            // error_log(str_replace('#_', 'jos', $entity->taggables->getQuery()));

            return $entity;
        }

        return;
    }

    /**
     * Browse Service.
     *
     * @param AnCommandContext $context
     */
    protected function _actionBrowse(AnCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);

        if(in_array($this->sort, array('top', 'trending')) && $this->q == '') {
            $package = $this->getIdentifier()->package;

            $entities->select('COUNT(*) AS count')
            ->join('RIGHT', 'edges AS edge', '@col(id) = edge.node_a_id')
            ->where('edge.type', 'LIKE', '%com:'.$package.'.domain.entity.tag')->group('@col(id)')
            ->order('count', 'DESC');

            if ($this->sort == 'trending') {
                $now = new AnDate();
                $entities->where('edge.created_on', '>', $now->addDays(-(int) $this->days)->getDate());
            }
        }

        return $entities;
    }

    /**
     * Set the default Tag View.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return ComTagsControllerDefault
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
    *  Method to fetch the taggable object
    *
    *
    */
    public function fetchTaggable(AnCommandContext $context)
    {
        $this->taggable = AnService::get('repos:nodes.node')
                           ->getQuery()
                           ->disableChain()
                           ->id($this->taggable_id)
                           ->fetch();

        if(!$this->taggable) {
            throw new LibBaseControllerExceptionNotFound('Locatable object does not exist');
        }

        return $this->taggable;
    }
}
