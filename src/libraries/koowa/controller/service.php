<?php
/**
 * @version		$Id: service.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Bread Controller Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Controller
 */
abstract class KControllerService extends KControllerResource
{
 	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'behaviors'  => array('discoverable', 'editable'),
    	    'readonly'   => false,
        ));

        parent::_initialize($config);
    }

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	KControllerException	If the identifier is not a view identifier
	 * @return	KControllerAbstract
	 */
    public function setView($view)
	{
	    if(is_string($view) && strpos($view, '.') === false )
		{
		    if(!isset($this->_request->view))
		    {
		        if($this->getModel()->getState()->isUnique()) {
			        $view = KInflector::singularize($view);
		        } else {
			        $view = KInflector::pluralize($view);
	    	    }
		    }
		}

		return parent::setView($view);
	}

	/**
	 * Generic browse action, fetches a list
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRowset	A rowset object containing the selected rows
	 */
	protected function _actionBrowse(KCommandContext $context)
	{
		$data = $this->getModel()->getList();
		return $data;
	}

	/**
	 * Generic read action, fetches an item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow	A row object containing the selected row
	 */
	protected function _actionRead(KCommandContext $context)
	{
	    $data = $this->getModel()->getItem();
	    $name = ucfirst($this->getView()->getName());

		if($this->getModel()->getState()->isUnique() && $data->isNew()) {
		    $context->setError(new KControllerException($name.' Not Found', KHttpResponse::NOT_FOUND));
		}

		return $data;
	}

	/**
	 * Generic edit action, saves over an existing item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRowset A rowset object containing the updated rows
	 */
	protected function _actionEdit(KCommandContext $context)
	{
	    $data = $this->getModel()->getData();

	    if(count($data))
	    {
	        $data->setData(KConfig::unbox($context->data));

	        //Only set the reset content status if the action explicitly succeeded
	        if($data->save() === true) {
		        $context->status = KHttpResponse::RESET_CONTENT;
		    } else {
		        $context->status = KHttpResponse::NO_CONTENT;
		    }
		}
		else $context->setError(new KControllerException('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	/**
	 * Generic add action, saves a new item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the new data
	 */
	protected function _actionAdd(KCommandContext $context)
	{
		$data = $this->getModel()->getItem();

		if($data->isNew())
		{
		    $data->setData(KConfig::unbox($context->data));

		    //Only throw an error if the action explicitly failed.
		    if($data->save() === false)
		    {
			    $error = $data->getStatusMessage();
		        $context->setError(new KControllerException(
		           $error ? $error : 'Add Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
		        ));

		    }
		    else $context->status = KHttpResponse::CREATED;
		}
		else $context->setError(new KControllerException('Resource Already Exists', KHttpResponse::BAD_REQUEST));

		return $data;
	}

	/**
	 * Generic delete function
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRowset	A rowset object containing the deleted rows
	 */
	protected function _actionDelete(KCommandContext $context)
	{
	    $data = $this->getModel()->getData();

		if(count($data))
	    {
            $data->setData(KConfig::unbox($context->data));

            //Only throw an error if the action explicitly failed.
	        if($data->delete() === false)
	        {
			    $error = $data->getStatusMessage();
                $context->setError(new KControllerException(
		            $error ? $error : 'Delete Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
		        ));
		    }
		    else $context->status = KHttpResponse::NO_CONTENT;
		}
		else  $context->setError(new KControllerException('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	/**
	 * Get action
	 *
	 * This function translates a GET request into a read or browse action. If the view name is
	 * singular a read action will be executed, if plural a browse action will be executed.
	 *
	 * If the result of the read or browse action is not a row or rowset object the fucntion will
	 * passthrough the result, request the attached view to render itself.
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	string|false 	The rendered output of the view or FALSE if something went wrong
	 */
	protected function _actionGet(KCommandContext $context)
	{
		//Check if we are reading or browsing
	    $action = KInflector::isSingular($this->getView()->getName()) ? 'read' : 'browse';

	    //Execute the action
		$result = $this->execute($action, $context);

		//Only process the result if a valid row or rowset object has been returned
		if(($result instanceof KDatabaseRowInterface) || ($result instanceof KDatabaseRowsetInterface)) {
            $result = parent::_actionGet($context);
		}

		return (string) $result;
	}

	/**
	 * Post action
	 *
	 * This function translated a POST request action into an edit or add action. If the model
	 * state is unique a edit action will be executed, if not unique an add action will
	 * be executed.
	 *
	 * @param	KCommandContext		A command context object
	 * @return 	KDatabaseRow(set)	A row(set) object containing the modified data
	 */
	protected function _actionPost(KCommandContext $context)
	{
		$action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
		return parent::execute($action, $context);
	}

	/**
	 * Put action
	 *
	 * This function translates a PUT request into an edit or add action. Only if the model
	 * state is unique and the item exists an edit action will be executed, if the resources
	 * doesn't exist and the state is unique an add action will be executed.
	 *
	 * If the resource already exists it will be completely replaced based on the data
	 * available in the request.
	 *
	 * @param	KCommandContext			A command context object
	 * @return 	KDatabaseRow(set)		A row(set) object containing the modified data
	 * @throws  KControllerException 	If the model state is not unique
	 */
	protected function _actionPut(KCommandContext $context)
	{
	    $data = $this->getModel()->getItem();

	    if($this->getModel()->getState()->isUnique())
	    {
            $action = 'add';
	        if(!$data->isNew())
	        {
	            //Reset the row data
	            $data->reset();
	            $action = 'edit';
            }

            //Set the row data based on the unique state information
	        $state = $this->getModel()->getState()->getData(true);
	        $data->setData($state);

            $data = parent::execute($action, $context);
	    }
	    else $context->setError(new KControllerException(ucfirst('Resource not found', KHttpResponse::BAD_REQUEST)));

	    return $data;
	}
}