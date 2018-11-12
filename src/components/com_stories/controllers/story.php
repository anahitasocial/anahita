<?php

/**
 * Story Controller.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       https://www.GetAnahita.com
 */
class ComStoriesControllerStory extends ComBaseControllerService
{
    /** 
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_state->insert('name');
    }

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
            'behaviors' => array(
                'serviceable' => array('except' => array('edit')),
                'ownable' => array('default' => get_viewer()),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Creates a new story. This is an internal method and can not be
     * called from outside. 
     * 
     * Check 
     * ComStoriesControllerPermissionStory::canAdd
     * 
     * (non-PHPdoc)
     *
     * @see ComBaseControllerService::_actionAdd()
     */
    protected function _actionAdd(AnCommandContext $context)
    {
        $data = $context->data;

        return $this->getRepository()->create($data->toArray());
    }

    /**
     * Browse action.
     * 
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionBrowse($context)
    {
        $query = $this->getRepository()->getQuery()->limit($this->limit, $this->start);

        if ($this->filter == 'leaders') {
            $ids = get_viewer()->leaderIds->toArray();
            $ids[] = get_viewer()->id;
            $query->where('owner.id', 'IN', $ids);
        } else {
            $query->owner($this->actor);
        }

        $query->aggregateKeys($this->getService('com://site/stories.domain.aggregations'));

        $query->order('creationTime', 'desc');

        if ($this->component) {
            $query->clause()->component((array) AnConfig::unbox($this->component));
        }

        if ($this->name) {
            $query->clause()->name((array) AnConfig::unbox($this->name));
        }

        if ($this->subject) {
            $query->clause()->where('subject.id', 'IN', (array) AnConfig::unbox($this->subject));
        }

        return $this->setList($query->toEntitySet())->getList();
    }

    /**
     * Delete a story.
     * 
     * @return bool
     */
    protected function _actionDelete($context)
    {
        $context->response->status = AnHttpResponse::NO_CONTENT;
        $this->getItem()->delete();
        $context->response->setRedirect(route($this->getItem()->owner->getURL()));
    }
}
