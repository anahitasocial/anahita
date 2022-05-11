<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleViewSessionJson extends ComBaseViewJson
{
    public function display()
    {
        if (! $this->_state->getItem()) {
            throw new LibBaseControllerExceptionUnauthorized('User is not logged in');
        } else {
            return parent::display();
        }
    }
    
    /**
     * Serializes an item into an array.
     *
     * @return array
     */
    protected function _serializeToArray($item)
    {
        $data = array();
        $serializer = $this->getService('com:actors.domain.serializer.actor');
        $data = $serializer->toSerializableArray($item);

        $data['username'] = $item->username;
        $data['givenName'] = $item->givenName;
        $data['familyName'] = $item->familyName;
        $data['email'] = $item->email;
        $data['usertype'] = $item->usertype;
        $data['gender'] = $item->gender;
        
        $context = new AnCommandContext();
        $context->gadgets = new LibBaseTemplateObjectContainer();
        $context->composers = new LibBaseTemplateObjectContainer();
        $context->actor = $item;

        $components = $this->getService('repos:components.component')->fetchSet();
        $components->registerEventDispatcher($this->getService('anahita:event.dispatcher'));
        
        $this->getService('anahita:event.dispatcher')->dispatchEvent('onDashboardDisplay', $context);
        
        // error_log(print_r(array_keys($context->composers->getObjects()), true));
        
        $data['composers'] = array_keys($context->composers->getObjects());
        $data['gadgets'] = array_keys($context->gadgets->getObjects());

        return $data;
    }
}
