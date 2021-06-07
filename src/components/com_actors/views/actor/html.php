<?php

/**
 * Default Actor View (Profile View).
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsViewActorHtml extends ComBaseViewHtml
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
            'template_paths' => array(
                dirname(__FILE__).'/html',
                ANPATH_THEMES.'/'.$this->getService('application')->getTemplate().'/html/com_actors/actor'
            ),
        ));

        parent::_initialize($config);

        $this->enabled_apps = null;
    }

    /**
     * Default layout.
     */
    protected function _layoutDefault()
    {
        $context = new AnCommandContext();

        $context->gadgets = new LibBaseTemplateObjectContainer();
        $context->actor = $this->_state->getItem();
        $context->composers = new LibBaseTemplateObjectContainer();
        $context->commands = $this->getTemplate()->renderHelper('toolbar.commands', 'toolbar');
        $context->profile = new AnConfig();

        $context->gadgets->insert('socialgraph', array(
            'title' => translate(array('COM-ACTORS-GADGET-LABEL-SOCIALGRAPH', 'COM-'.strtoupper($this->getIdentifier()->package).'-GADGET-LABEL-SOCIALGRAPH')),
            'url' => $context->actor->getURL().'&get=graph&layout=gadget_profile',
            'title_url' => $context->actor->getURL().'&get=graph',
        ));

        if ($context->actor->authorize('access')) {

            $context->actor->components->registerEventDispatcher($this->getService('anahita:event.dispatcher'));

            $this->getService('anahita:event.dispatcher')->dispatchEvent('onProfileDisplay', $context);

            dispatch_plugin('profile.onDisplay', array('actor' => $this->_state->getItem(), 'profile' => $context->profile));

            $this->profile = $context->profile;

            if (count($context->profile) > 0) {
                $context->gadgets->insert('information', array(
                    'title' => translate(array('COM-ACTORS-GADGET-LABEL-ACTOR-INFO', 'COM-'.strtoupper($this->getIdentifier()->package).'-GADGET-LABEL-ACTOR-INFO')),
                    'content' => $this->load('info'),
                ));
            }
        }

        $context->gadgets->sort(array('stories', 'information'));

        $this->set(array(
            'commands' => $context->commands,
            'gadgets' => $context->gadgets,
            'composers' => $context->composers,
        ));
    }

    /**
     * Default Badge.
     *
     * @return
     */
    protected function _layoutBadge()
    {
        $context->commands = $this->getTemplate()->renderHelper('toolbar.commands', 'toolbar');

        $this->set(array(
            'commands' => $context->commands,
        ));
    }
}
