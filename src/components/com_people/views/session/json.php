<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleViewSessionJson extends ComBaseViewJson
{
    public function display()
    {
        if (!$this->_state->getItem()) {
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

        return $data;
    }
}
