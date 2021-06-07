<?php

/**
 * Abstract Actor Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComActorsControllerAbstract extends ComBaseControllerService
{
    /**
     * The max upload limit.
     *
     * @var int
     */
    protected $_max_upload_limit;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback(array(
            'after.delete',
            'after.add',
            ), array($this, 'redirect'));

        //set filter state
        $this->getState()->insert('filter');

        $this->_max_upload_limit = $config->max_upload_limit;
    }

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
            'max_upload_limit' => (int) ini_get('upload_max_filesize'),
        ));

        parent::_initialize($config);

        $config->append(array(
            'behaviors' => to_hash(array(
                'com:search.controller.behavior.searchable',
                'com:stories.controller.behavior.publisher',
                'com:notifications.controller.behavior.notifier',
                'followable',
                'administrable',
                'requestable',
                'appable',
                'permissionable',
                'ownable',
                'privatable',
                'enablable',
                'verifiable',
                'subscribable',
                'com:hashtags.controller.behavior.hashtaggable',
                'com:locations.controller.behavior.geolocatable',
                'coverable',
            )),
        ));
    }

    /**
     * Browse Action.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetDefault
     */
    protected function _actionBrowse(AnCommandContext $context)
    {
        $context->append(array(
            'query' => $this->getRepository()->getQuery(),
        ));

        $query = $context->query;

        if ($this->q) {
            $query->keyword($this->getService('anahita:filter.term')
                                 ->sanitize($this->q));
        }

        if ($this->ids) {
            $ids = AnConfig::unbox($this->ids);
            $query->id($ids);
        } else {
            $query->limit($this->limit, $this->start);
        }

        $entities = $query->toEntitySet();

        if ($this->isOwnable() && $this->actor) {
            $this->_state->append(array(
                'filter' => 'following',
            ));

            if (
                $this->filter == 'administering' &&
                $this->getRepository()->hasBehavior('administrable')
                ) {
                $entities->where('administrators.id', 'IN', array($this->actor->id));
            } elseif ($this->actor->isFollowable()) {
                $entities->where('followers.id', 'IN', array($this->actor->id));
            }
        }

        $entities->order('created_on', 'desc');

        return $this->setList($entities)->getList();
    }

    /**
     * Add an actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd(AnCommandContext $context)
    {
        $entity = parent::_actionAdd($context);

        if ($entity->isPortraitable() && AnRequest::has('files.portrait')) {
            $file = AnRequest::get('files.portrait', 'raw');

            if ($this->bellowSizeLimit($file) && $file['error'] == 0) {
                $entity->setPortrait(array(
                   'url' => $file['tmp_name'],
                   'mimetype' => $file['type'],
                   )
               );
            }
        }
        
        if ($entity->save($context)) {
            dispatch_plugin('profile.onSave', array(
                'actor' => $entity,
                'data' => $context->data,
                ));
        }

        return $entity;
    }

    /**
     * Edit's an actor data.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit(AnCommandContext $context)
    {
        $entity = parent::_actionEdit($context);
        
        if ($this->viewer->admin() && array_key_exists('enabled', $_POST)) {
            AnRequest::get('post.enabled', 'boolean') ? $entity->enable() : $entity->disable();
        }

        if ($entity->isPortraitable() && AnRequest::has('files.portrait')) {
            $file = AnRequest::get('files.portrait', 'raw');

            if ($this->bellowSizeLimit($file) && $file['error'] == 0) {
                $this->getItem()->setPortrait(array(
                    'url' => $file['tmp_name'],
                    'mimetype' => $file['type']
                ));
            } else {
                $this->getItem()->removePortraitImage();
            }
        }

        if ($entity->save($context)) {
            dispatch_plugin('profile.onSave', array(
                'actor' => $entity,
                'data' => $context->data,
                ));
        }

        return $entity;
    }

    /**
     * Set the default Actor View.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return ComActorsControllerDefault
     */
    public function setView($view)
    {
        parent::setView($view);

        if (!$this->_view instanceof ComBaseViewAbstract) {
            $name = AnInflector::isPlural($this->view) ? 'actors' : 'actor';
            $defaults[] = 'ComActorsView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComActorsView'.ucfirst($name).ucfirst($this->_view->name);
            register_default(array(
                'identifier' => $this->_view,
                'default' => $defaults,
                ));
        }

        return $this;
    }

    /**
     * Deletes an actor and all of the necessary cleanup. It also dispatches all the apps to
     * clean up after the deleted actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        $this->getService('repos:components')
             ->fetchSet()
             ->registerEventDispatcher($this->getService('anahita:event.dispatcher'));

        $result = parent::_actionDelete($context);

        $this->getService('anahita:event.dispatcher')
             ->dispatchEvent('onDeleteActor', array(
                'actor_id' => $this->getItem()->id,
                ));

        return $result;
    }

    /**
     * Get a toolbar by identifier.
     *
     * @return AnControllerToolbarAbstract
     */
    public function getToolbar($toolbar, $config = array())
    {
        if (is_string($toolbar)) {
            //if actorbar or menu alawys default to the base
            if (in_array($toolbar, array('actorbar'))) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller','toolbar');
                $identifier->name = $toolbar;
                register_default(array(
                    'identifier' => $identifier,
                    'default' => 'ComActorsControllerToolbar'.ucfirst($toolbar),
                    ));
                $toolbar = $identifier;
            }
        }

        return parent::getToolbar($toolbar, $config);
    }

    /**
     * Set the necessary redirect.
     *
     * @param AnCommandContext $context
     */
    public function redirect(AnCommandContext $context)
    {
        $url = null;

        if ($context->action == 'delete') {
            $url = 'option=com_'.$this->getIdentifier()->package.'&view='.AnInflector::pluralize($this->getIdentifier()->name);
        } elseif ($context->action == 'add') {
            $url = $context->result->getURL().'&get=settings';
        }

        if ($url) {
            $context->response->setRedirect(route($url));
        }
    }

    /**
     * Checks to see whether the uploaded file exceeds the allowed limit.
     *
     * @param posted file request
     *
     * @return bool
     */
    public function bellowSizeLimit($file)
    {
        $content = @file_get_contents($file['tmp_name']);
        $filesize = strlen($content);
        $uploadlimit = $this->_max_upload_limit * 1024 * 1024;

        if ($filesize > $uploadlimit) {
            throw new LibBaseControllerExceptionBadRequest('Exceed maximum size');
        }

        return true;
    }
}
