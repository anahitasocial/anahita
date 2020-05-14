<?php

/**
* Geolocatable Behavior.
*
* @category   Anahita
*
* @author     Rastin Mehr <rastin@anahitapolis.com>
* @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
*
* @link       http://www.GetAnahita.com
*/
class ComLocationsControllerBehaviorGeolocatable extends AnControllerBehaviorAbstract
{
   /*
   *   temporary location object for adding and removing to a gelocatable
   */
   protected $_location = null;
   
   /*
   *   temporary location controller object for rendering a location object
   */
   protected $_location_controller = null;

   /**
    * Constructor.
    *
    * @param AnConfig $config An optional AnConfig object with configuration options.
    */
   public function __construct(AnConfig $config)
   {
       parent::__construct($config);

       $this->registerCallback(array(
         'before.addLocation',
         'before.deleteLocation'
       ), array($this, 'fetchLocation'));
   }

   /**
   *  Method to add a location to a geolocatable node.
   *  If the location node doesn't exist, create it.
   *
   *  @param AnCommandContext $context
   *  @return instance of ComBaseDomainEntityNode entity with gelocatable behavior
   */
   protected function _actionAddlocation(AnCommandContext $context)
   {
       $this->getItem()->addLocation($this->_location);
       
       if ($this->isDispatched()) {
           $context->response->content = $this->getLocationController()
           ->setItem($this->_location)
           ->display();
       }
       
       return $this->_location;
   }

   /**
   *  Method to remove a location from a geolocatable node
   *
   *
   *  @param AnCommandContext $context
   *  @return instance of ComBaseDomainEntityNode entity with gelocatable behavior
   */
   protected function _actionDeletelocation(AnCommandContext $context)
   {
       $this->getItem()->deleteLocation($this->_location);
       return $this->_location;
   }

   /**
   *   Method to fetch or create a location enitty
   *
   *   @param AnCommandContext $context
   *   @return ComLocationsDomainEntityLocation entity
   */
   public function fetchLocation(AnCommandContext $context)
   {
       $data = $context->data;

       if($data->location_id){
           $attr = array('id' => $data->location_id);
       } else {
           $attr = array(
              'latitude' => $data->latitude,
              'longitude' => $data->longitude,
              'name' => $data->name,
              'address' => $data->address,
              'city' => $data->city,
              'state_province' => $data->state_province,
              'country' => $data->country,
              'postalcode' => $data->postalcode
           );
       }

       $this->_location = $this->getService('repos:locations.location')->findOrAddNew($attr);

       return $this->_location;
   }
   
   public function getLocationController()
   {
       if (! isset($this->_location_controller)) {
           $identifier = 'com:locations.controller.location';
           $request = new LibBaseControllerRequest(array('format' => $this->getRequest()->getFormat()));

           $this->_location_controller = $this->getService($identifier, array(
               'read_only' => true,
               'request' => $request,
               'response' => $this->getResponse(),
           ));
       }
       
       return $this->_location_controller;
   }

   /**
   * returns true if the viewer can add a location to this locatable
   *
   * @return boolean
   */
   public function canAddlocation()
   {
      return $this->getItem()->authorize('edit');
   }

   /**
   * returns true if the viewer can delete a location from this locatable
   *
   * @return boolean
   */
   public function canDeletelocation()
   {
      return $this->getItem()->authorize('edit');
   }
}
