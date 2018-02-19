<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Story Parser Template Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComStoriesTemplateHelperParser extends LibBaseTemplateHelperAbstract
{
    /**
     * Parse Template.
     * 
     * @var ComBaseTemplateDefault
     */
    protected $_template;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $identfier = clone $this->getIdentifier();
        $identfier->path = array('template');
        $identfier->name = 'parser';

        register_default(array('identifier' => $identfier, 'default' => 'ComBaseTemplateDefault'));

        $this->_template = $this->getService($identfier);

        foreach ($config->filters as $filter) {
            $this->_template->addFilter($filter);
        }

        $this->_template->getFilter('alias')->append(KConfig::unbox($config->alias));

        $this->getService('anahita:language')->load('com_stories');

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
        $config->append(array(
            'paths' => array(
                ANPATH_ROOT.'/components/com_stories/templates/stories',
                ANPATH_ROOT.'/components/com_actors/templates/stories',
                ),
            'filters' => array('alias', 'shorttag'),
            'alias' => array(
                '@escape(' => 'htmlspecialchars(',
                '@route(' => 'route(',
                '@name(' => '$this->renderHelper(\'com://site/actors.template.helper.story.names\',',
                '@possessive(' => '$this->renderHelper(\'com://site/stories.template.helper.story.possessiveNoune\',$story,',
                '@link(' => '$this->renderHelper(\'com://site/actors.template.helper.story.link\',',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Render a story. If a $actor is passed then we are rendering the stories related to an actor. (A profile stories
     * as opposed to.
     * 
     * @param ComStoriesDomainEntityStory $story Story       
     * @param ComActorsDomainEntityActor  $actor Actor
     * 
     * @return array
     */
    public function parse($story, $actor = null)
    {
        $options = array();

        $this->getService('anahita:language')->load($story->component);

        static $commands;

        $commands = $commands ? clone $commands : new LibBaseTemplateObjectContainer();

        $commands->reset();

        $data = array(
            'commands' => $commands,
            'actor' => $actor,
            'helper' => $this,
            'story' => $story,
            'subject' => $story->subject,
            'target' => $story->target,
            'object' => $story->object,
            'comment' => $story->comment,
            'type' => $story->getIdentifier()->name,
        );

        $path[] = ANPATH_ROOT.'/components/'.$story->component.'/templates/stories/'.$story->name.'.php';

        $output = $this->_render($story, $path, $data);
        $data = $this->_parseData($output);

        $data['commands'] = $commands;

        return $data;
    }

    /**
     * Renders a story.
     *
     * @param array $paths
     * @param array $data
     */
    protected function _render($story, $paths, $data)
    {
        settype($paths, 'array');

        foreach ($paths as $path) {
            if ($this->_template->findFile($path)) {
                return $this->_template->loadFile($path, $data)->render();
            }
        }

        try {
            return $this->_template->loadTemplate($story->name, $data)->render();
        } catch (Exception $e) {
            print '<small>file missing :'.$path.'</small>';
        }
    }

    /**
     * Parse the title,body from data.
     *
     * @param string $data
     *
     * @return array
     */
    protected function _parseData($data)
    {
        $output = array('title' => '','body' => '');
        $matches = array();

        if (preg_match_all('#<data name="([^"]+)">(.*?)<\/data>#si', $data, $matches)) {
            $attributes = $matches[1];
            $contents = $matches[2];

            foreach ($attributes as $i => $attribute) {
                $output[$attribute] = $contents[$i];
            }
        }

        return $output;
    }

    /**
     * Return the parse template.
     *
     * @return ComBaseTemplateDefault
     */
    public function getTemplate()
    {
        return $this->_template;
    }
}
