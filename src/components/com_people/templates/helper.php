<?php

/**
 * People Template Helper.
 *
 * Provides methods to for rendering avatar/name for an actor
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleTemplateHelper extends KTemplateHelperAbstract
{
    /**
     * Return the list of enabled app links on an actor's profile.
     *
     * @param actor object ComActorsDomainEntityActor
     *
     * @return array LibBaseTemplateObjectContainer
     */
    public function viewerMenuLinks($actor)
    {
        $context = new KCommandContext();
        $context->menuItems = new LibBaseTemplateObjectContainer();
        $context->actor = $actor;
        $context->actor->components->registerEventDispatcher($this->getService('anahita:event.dispatcher'));
        $this->getService('anahita:event.dispatcher')->dispatchEvent('onMenuDisplay', $context);

        return $context->menuItems;
    }

    /**
     * Displays selector for person usertypes.
     *
     * @param array of options
     *
     * @return html select
     */
    public function usertypes($options = array())
    {
        $viewer = get_viewer();
        $options = new KConfig($options);

        $options->append(array(
            'id' => 'person-userType',
            'selected' => 'registered',
            'name' => 'usertype',
            'class' => 'input-block-level',
        ));

        $selected = $options->selected;

        unset($options->selected);

        $usertypes = array(
            ComPeopleDomainEntityPerson::USERTYPE_REGISTERED => AnTranslator::_('COM-PEOPLE-USERTYPE-REGISTERED'),
            ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR => AnTranslator::_('COM-PEOPLE-USERTYPE-ADMINISTRATOR'),
        );

        if ($viewer->superadmin()) {
            $usertypes[ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR] = AnTranslator::_('COM-PEOPLE-USERTYPE-SUPER-ADMINISTRATOR');
        }

        $html = $this->getService('com:base.template.helper.html');

        return $html->select($options->name, array('options' => $usertypes, 'selected' => $selected), KConfig::unbox($options));
    }
}
