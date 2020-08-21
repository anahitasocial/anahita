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
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Default Setting View.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsViewSettingsHtml extends ComBaseViewHtml
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
                ANPATH_THEMES.'/'.$this->getService('application')->getTemplate().'/html/com_actors/settings'
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Layout Profile.
     */
    protected function _layoutProfile()
    {
        $this->profile = new AnConfig();

        dispatch_plugin('profile.onEdit', array('actor' => $this->_state->getItem(), 'profile' => $this->profile));
    }

    /**
     * Layout Privacy.
     */
    protected function _layoutPermissions()
    {
        $components = array();
        foreach ($this->_state->getItem()->components as $component) {
            $permissions = array();

            if (!$component->isAssignable()) {
                continue;
            }

            if (!count($component->getPermissions())) {
                continue;
            }

            foreach ($component->getPermissions() as $identifier => $actions) {
                if (strpos($identifier, '.') === false) {
                    $name = $identifier;
                    $identifier = clone $component->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;
                }

                $identifier = $this->getIdentifier($identifier);

                foreach ($actions as $action) {
                    $label = AnTranslator::_(strtoupper('COM-'.$identifier->package.'-PERMISSION'.'-'.$identifier->name.'-'.$action));
                    $name = 'com_'.$identifier->package.':'.$identifier->name.':'.$action;
                    $permissions[] = new AnConfig(array('label' => $label, 'name' => $name));
                }
            }

            $component->permissions = $permissions;
            $components[] = $component;
        }

        $this->components = $components;
    }

    /**
     * Default Layout.
     */
    protected function _layoutDefault()
    {
        $this->edit = pick($this->edit, 'profile');

        $tabs = new LibBaseTemplateObjectContainer();

        $tabs->insert('profile', array(
            'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-PROFILE'),
        ));

        $tabs->insert('avatar', array(
           'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-AVATAR'),
        ));

        $tabs->insert('cover', array(
           'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-COVER'),
        ));
        
        $tabs->insert('privacy', array(
            'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-PRIVACY'),
        ));

        $tabs->insert('permissions', array(
            'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-PERMISSIONS'),
        ));

        if ($this->_state->getItem()->isFollowable() && $this->_state->getItem()->followRequesterIds->count() > 0) {
            $tabs->insert('requests', array(
                'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-REQUESTS').'<span class="pull-right badge badge-important">'.$this->_state->getItem()->followRequesterIds->count().'</span>',
            ));
        }

        if ($this->_state->getItem()->isAdministrable()) {
            $tabs->insert('admins', array(
                'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-ADMINS'),
            ));
        }

        //lets get a list of components that this actor can enable
        $this->apps = $this->getService('com:actors.domain.entityset.component', array(
                'actor' => $this->_state->getItem(),
                'can_enable' => true,
            ));

        if (count($this->apps)) {
            $tabs->insert('apps', array(
                'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-APPS'),
            ));
        }

        if ($this->_state->getItem()->authorize('delete')) {
            $tabs->insert('delete', array(
                   'label' => AnTranslator::_('COM-ACTORS-PROFILE-EDIT-TAB-DELETE'),
            ));
        }

        $this
        ->getService('anahita:event.dispatcher')
        ->dispatchEvent('onSettingDisplay', array(
            'actor' => $this->_state->getItem(),
            'tabs' => $tabs,
        ));

        $url = $this->_state->getItem()->getURL(false).'&get=settings&edit=';

        $active_tab = $tabs['profile'];

        foreach ($tabs as $tab) {
            $tab->url = $url.$tab->getName();

            if ($tab->name == $this->edit) {
                $active_tab = $tab;
            }
        }

        $active_tab->active = true;

        $this->tabs = $tabs;

        if ($active_tab->content) {
            $this->content = $active_tab->content;
        } elseif ($active_tab->controller) {
            $this->content = $this->getService($active_tab->controller)->oid($this->_state->getItem()->id)->display();
        } else {
            $this->content = $this->load(pick($active_tab->layout, $active_tab->name), array('url' => $url));
        }
    }
}
