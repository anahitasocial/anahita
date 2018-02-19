<?php

/**
 * UI Template Helper. Render common ui.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateHelperUi extends LibBaseTemplateHelperAbstract
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_template->addSearchPath(KConfig::unbox($config->paths), true);
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
        $path[] = dirname(__FILE__).'/ui';

        $config->append(array(
            'paths' => $path,
        ));

        if (!$config->template) {
            $template = $this->getService('com:base.template.default');
            $template->addFilter('alias')->addFilter('shorttag');
            $config->append(array(
                 'template' => $template,
            ));
        }

        parent::_initialize($config);

        $paths = KConfig::unbox($config->paths);
        array_unshift($paths, ANPATH_THEMES.'/'.KService::get('application')->getTemplate().'/html/com_base/ui');
        $config->paths = $paths;
    }

    /**
     * Renders a header
     *
     * @param array $config
     *
     * @return string
     */
    public function header($config = array())
    {
      return $this->_render('header', $config);
    }

    /**
     * Render the message in the flash.
     *
     * @param array $config
     *
     * @return string
     */
    public function flash($config = array())
    {
        $data = $this->_template->getData();

        if (isset($data['flash']) && $data['flash']->message) {
            $message = array_merge((array) $data['flash']->getMessage(true), $config);
            return $this->message($message['message'], $message);
        }
    }

    /**
     * Renders a message.
     *
     * @param string $message
     * @param array  $config
     */
    public function message($message, $config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'type' => 'info',
            'block' => true,
            'closable' => false,
        ));

        $alertblock = ($config->block) ? 'alert-block' : '';

        $close_handler = '';
        if ($config->closable) {
            $close_handler = '<a class="close" data-dismiss="alert">&times;</a>';
        }

        $message = AnTranslator::_($message);

        return "<div class=\"alert alert-{$config->type} $alertblock\">$close_handler<p>$message</p></div>";
    }

    /**
     * Creates a dropdown from a list of commands.
     *
     * @param LibBaseTemplateObjectContainer $commands Container of commands
     * @param array                          $config   Configuration array
     *
     * @return string
     */
    public function dropdown($commands, $config = array())
    {
        $config['commands'] = $commands;

        if (!isset($config['icon'])) {
            $config['icon'] = 'cog';
        }

        return $this->_render('dropdown', $config);
    }

    /**
     * Renders a list of basic nodes.
     *
     * @param AnDomainEntityAbstract $entity The parent of the comments
     * @param array                  $config An array of configuration
     *
     * @return string
     */
    public function nodes($items, $config = array())
    {
        $config['items'] = $items;

        return $this->_render('nodes', $config);
    }

    /**
     * Renders the comments for a commentable.
     *
     * @param AnDomainEntityAbstract $entity The parent of the comments
     * @param array                  $config An array of configuration
     *
     * @return string
     */
    public function comments($entity, $config = array())
    {
        $config = array_merge(array(
            'truncate_body' => array(),
            'editor' => false,
            'pagination' => true,
            'show_guest_prompt' => true,
            'content_filter_exclude' => array(),
         ),

         $config);

        $data = $this->getTemplate()->getData();
        $limit = isset($data['limit'])  ? $data['limit']  : 0;
        $offset = isset($data['start'])  ? $data['start']  : 0;

        if (!isset($config['comments'])) {
            $config['comments'] = $entity->comments->order('creationTime', 'ASC')->limit($limit, $offset);
        }

        if (!isset($config['can_comment'])) {
            $config['can_comment'] = false;
            if ($entity && $entity->isAuthorizer()) {
                $config['can_comment'] = $entity->authorize('add.comment');
                //if can't comment then check if it needs to follow
                if ($config['can_comment'] === false && $entity->__require_follow) {
                    $config['require_follow'] = true;
                }
            }
        }

        if (!isset($config['strip_tags'])) {
            $config['strip_tags'] = false;
        }

        $config['entity'] = $entity;

        if ($config['pagination'] === $config['comments'] instanceof AnDomainEntitysetDefault) {
            $config['pagination'] = $this->pagination($config['comments'], array('paginate' => true, 'options' => array('scrollToTop' => true)));
        }

        return $this->_render('comments', $config);
    }

    /**
     * Renders a command as a link tag. If it has a href, it will use the route to Route the link.
     *
     * @param ComBaseControllerToolbarCommand $command Command to render as a link
     *
     * @return string
     */
    public function command($command)
    {
        $attributes = KConfig::unbox($command->getAttributes());

        if (isset($attributes['icon'])) {
            $icon = 'icon-'.$attributes['icon'];
            $command->label = '<i class="'.$icon.'"></i>&nbsp;'.$command->label;
            unset($attributes['icon']);
        }

        $html = $this->getService('com:base.template.helper.html');

        return (string) $html->tag('a', $command->label, $attributes);
    }

    /**
     * Renders a gadget with a title and a body.
     *
     * @param LibBaseTemplateObject $gadget Gadget object
     *
     * @return string
     */
    public function gadget($gadget)
    {
        return $this->_render('gadget', array('gadget' => $gadget));
    }

    /**
     * Renders a list of voters for an entity.
     *
     * @param AnDomainEntitysetDefault $entity
     * @param array                    $config
     *
     * @return string
     */
    public function voters($entity, $config = array())
    {
        $viewer = $this->getService('com:people.viewer');

        $config = array_merge(array(
               'avatars' => false,
               'viewer' => $viewer,
        ), $config);

        if (!$config['avatars']) {
            return $this->_render('voters', array('entity' => $entity, 'viewer' => $viewer));
        } else {
            return $this->_render('voters_avatars', array('entity' => $entity, 'viewer' => $viewer));
        }
    }

    /**
    * Renders an InfinitScroll and Masonry layout if there are more than one columns
    *
    * @param an entity set extended from AnDomainEntitysetAbstract class
    * @param array of configs
    *
    * @return html
    */
    public function infinitescroll($entities, $config = array())
    {
        $config = array_merge(array(
            'entities' => (isset($entities)) ? $entities : null,
            'filter' => null,
            'entity_type' => (isset($entities)) ? $entities->getIdentifier()->name : null,
            'id' => null,
            'columns' => 2,
            'layout_list' => 'default',
            'layout_item' => 'list',
            'start' => 0,
            'limit' => 20,
            'hiddenlink' => false,
        ), $config);

        $config = array_merge(array(
            'url' => array(
                'layout' => 'list',
                'start' => $config['start'],
                'limit' => $config['limit'])
        ), $config);

        return $this->_render('infinitescroll', $config);
    }

    /**
     * Renders a pagination using the paginator object.
     *
     * @param AnDomainEntitysetDefault|KConfigPaginator $paginator Paginator object
     * @param array                                     $config    Configuration
     *
     * @returns string
     */
    public function pagination($paginator, $config = array())
    {
        if (!$paginator) {
            return '';
        }

        $config = new KConfig($config);

        $config->append(array(
            'url' => (string) $this->_template->getView()->getRoute(),
            'force' => false,
            'options' => array(
                'limit' => 20,
            ),
        ));

        if (is($paginator, 'AnDomainEntitysetDefault')) {
            $entityset = $paginator;
            $paginator = new KConfigPaginator(array(
                'offset' => $entityset->getOffset(),
                'limit' => 20,
            ));

            $paginator->total = $entityset->getTotal();
        }

        if (!$paginator instanceof KConfigPaginator) {
            $paginator = new KConfigPaginator($paginator);
        }

        $paginator->display = 5;

        //if our total is less than the limit don't show paginator
        if ($paginator->total < $paginator->limit) {
            return;
        }

        $config->paginator = $paginator;

        //convert url to the httpurl object
        $config->url = $this->getService('koowa:http.url', array('url' => $config->url));

        $pages = array();

        foreach ($paginator->pages->offsets as $offset) {
            $page = array();
            $url = clone $config->url;
            $query = array_merge($url->getQuery(true), array('limit' => $offset->limit, 'start' => $offset->offset));

            $url->setQuery($query);

            $page['number'] = $offset->page;
            $page['current'] = $offset->current;
            $page['url'] = (string) $url;

            $pages[] = $page;
        }

        $config['pages'] = $pages;
        $config['total'] = $paginator->total;
        $pages = array('prev','next');

        foreach ($pages as $page) {
            if ($paginator->pages->$page) {
                $url = clone $config->url;
                $query = array_merge($url->getQuery(true), array('limit' => $paginator->pages->$page->limit, 'start' => $paginator->pages->$page->offset));

                $url->setQuery($query);
                $config->{$page.'_page'} = (string) $url;
            }
        }

        return $this->_render('pagination', $config);
    }

    /**
     * Renders an editor.
     *
     * @param array $options
     *
     * @return string
     */
    public function editor($config)
    {
        $config = new KConfig($config);

        $config->append(array(
            'name' => 'description',
            'content' => '',
            'value' => '',
        ));

        $config->append(array(
            'html' => array(
                'id' => $config->name,
                    'width' => '100%',
                    'height' => '500',
                    'cols' => '75',
                    'rows' => '20',
                ),
        ));

        if (!$config->content) {
            $config->content = '';
        }

        $tags = $this->getService('com:base.template.helper.html');
        $textarea = $tags->textarea($config->name, $config->content, KConfig::unbox($config->html));
        $textarea->set('data-behavior', 'Editor')->id(rand());

        return  $textarea;
    }

    /**
     * Renders a privacy selector for a {@see LibBaseDomainBehaviorPrivatable} entity.
     *
     * @param array|AnDomainEntityAbstract A privatable entity
     *
     * @return string
     */
    public function privacy($config = array())
    {
        if ($config instanceof AnDomainEntityAbstract) {
            $config = new KConfig(array('entity' => $config));
        } else {
            $config = new KConfig($config);
        }

        $config->append(array(
            'auto_submit' => $config->entity && $config->entity->persisted(),
            'name' => 'access',
        ));

        if (!$config->options) {
            //need to know which graph options to render
            if ($config->entity->isOwnable()) {
                $config->options = $config->entity->owner;
            } elseif (is($config->entity, 'ComActorsDomainEntityActor')) {
                $config->options = $config->entity;
            }
        }

        if (is($config->options, 'ComActorsDomainEntityActor')) {
            $actor = $config->options;

            $options = new KConfig(array(
                LibBaseDomainBehaviorPrivatable::GUEST => AnTranslator::_('LIB-AN-PRIVACYLABEL-PUBLIC'),
                LibBaseDomainBehaviorPrivatable::REG => AnTranslator::_('LIB-AN-PRIVACYLABEL-REGISTERED'),
            ));

            if ($actor->isFollowable()) {
                $options->append(array(
                    LibBaseDomainBehaviorPrivatable::FOLLOWER => AnTranslator::_('LIB-AN-PRIVACYLABEL-FOLLOWERS'),
                ));
            }

            if ($actor->isLeadable()) {
                $options->append(array(
                        LibBaseDomainBehaviorPrivatable::LEADER => AnTranslator::_('LIB-AN-PRIVACYLABEL-LEADERS'),
                        LibBaseDomainBehaviorPrivatable::MUTUAL => AnTranslator::_('LIB-AN-PRIVACYLABEL-MUTUALS'),
                ));
            }

            if ($actor->isAdministrable()) {
                $options->append(array(
                    LibBaseDomainBehaviorPrivatable::ADMIN => AnTranslator::_('LIB-AN-PRIVACYLABEL-ADMIN'),
                ));
            } else {
                if (is_viewer($actor)) {
                    $options->append(array(
                        LibBaseDomainBehaviorPrivatable::ADMIN => AnTranslator::_('LIB-AN-PRIVACYLABEL-ONLYYOU'),
                    ));
                } else {
                    $options->append(array(
                        LibBaseDomainBehaviorPrivatable::ADMIN => sprintf(AnTranslator::_('LIB-AN-PRIVACYLABEL-ONLYNAME'), $actor->name),
                    ));
                }
            }

            foreach ($options as $key => $value) {
                if ($actor->authorize('setprivacyvalue', array('value' => $key)) === false) {
                    unset($options[$key]);
                }
            }

            $config->options = $options;

            if ($config->entity) {
                $config->append(array(
                    'selected' => $config->entity->getPermission($config->name, LibBaseDomainBehaviorPrivatable::FOLLOWER),
                ));
            }

            if (strpos($config->name, 'access') === false) {
                unset($config->options[LibBaseDomainBehaviorPrivatable::GUEST]);
            }

            //trim the options based on the actor
            if ($config->entity && !$config->entity->eql($actor)) {
                $current_index = array_search($actor->access, array_keys(KConfig::unbox($config->options)));
                $i = 0;

                foreach ($config->options as $key => $value) {
                    if ($current_index > $i) {
                        unset($config->options[$key]);
                    }

                    ++$i;
                }
            }
        }

        return $this->_render('privacy', $config);
    }

    /**
     * Renders a filterbox.
     *
     * @param array $config Configuration
     *
     * @returns	void
     */
    public function filterbox($path, $config = array())
    {
        if (is_array($path)) {
            $path = $this->_template->getView()->getRoute($path);
        }

        //remove q from the url
        $uri = $this->getService('koowa:http.url', array('url' => $path));
        $data = $uri->getQuery(true);

        unset($data['q']);

        $uri->setQuery($data);

        $path = (string) $uri;

        $placeholder = isset($config['placeholder']) ? $config['placeholder'] : AnTranslator::_('LIB-AN-FILTER-PLACEHOLDER');

        $config = new KConfig($config);

        $config->append(array(
            'search_action' => $path,
            'update_element' => false,
            'placeholder' => $placeholder
        ));

        return $this->_render('filterbox',  $config);
    }

    /**
     * Renders a search.
     *
     * @param array $config Configuration
     *
     * @returns	void
     */
    public function search($config = array())
    {
        $actor = null;
        $label = AnTranslator::_('LIB-AN-SEARCH-PLACEHOLDER');

        if ($this->getService()->has('com:search.owner')) {
            $actor = $this->getService('com:search.owner');
            $label = AnTranslator::sprintf('LIB-AN-SEARCH-PLACEHOLDER-OWNER', $actor->name);
        }

        $scope = null;

        if ($this->getService()->has('com:search.scope')) {
            $scope = $this->getService('com:search.scope');
        }

        $url = 'index.php?option=com_search';

        if ($actor) {
            $url .= '&oid='.$actor->uniqueAlias;
        }

        $term = KRequest::get('get.term', 'raw');
        $term = KService::get('com:search.filter.term')->sanitize($term);

        $config = new KConfig($config);

        $config->append(array(
            'label' => $label,
            'term' => $term,
            'url' => $url,
            'actor' => $actor,
            'scope' => $scope,
        ));

        return $this->_render('search',  $config);
    }

    /**
     * Render a form.
     *
     * @param array $inputs
     * @param array $options
     */
    public function form($inputs, $options = array())
    {
        return $this->_render('form', array('inputs' => $inputs, 'options' => new KConfig($options)));
    }

    /**
     * If a method is missing then just render a ui template using the method name.
     */
    public function __call($method, $arguments = array())
    {
        if (count($arguments) == 1 && (!is_array($arguments[0]) || is_numeric(key($arguments[0])))) {
            $arguments[0] = array($method => $arguments[0]);
        }

        return $this->_render($method, $arguments[0]);
    }

    /**
     * Renders a string using the template. We are using loadString as opposed to loadTempalte
     * or loadFile in order to avoid changing the state of the template.
     *
     * @param string $ui
     * @param array  $data
     *
     * @return string
     */
    protected function _render($ui, $data = array())
    {
        $data['helper'] = $this;
        $file = '_ui_'.$ui.'.php';
        $data = KConfig::unbox($data);

        return (string) $this->_template->loadTemplate('_ui_'.$ui, $data);
    }
}
