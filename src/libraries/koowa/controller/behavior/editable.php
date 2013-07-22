<?php
/**
 * @version		$Id: editable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Editable Controller Behavior Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage	Behavior
 */
class KControllerBehaviorEditable extends KControllerBehaviorAbstract
{
    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if ($this->isDispatched() && KRequest::type() == 'HTTP')
        {
            $this->registerCallback('before.read' , array($this, 'setReferrer'));
            $this->registerCallback('after.apply' , array($this, 'lockReferrer'));
			$this->registerCallback('after.read'  , array($this, 'unlockReferrer'));
	        $this->registerCallback('after.save'  , array($this, 'unsetReferrer'));
			$this->registerCallback('after.cancel', array($this, 'unsetReferrer'));
        }

		$this->registerCallback('after.read'  , array($this, 'lockResource'));
		$this->registerCallback('after.save'  , array($this, 'unlockResource'));
		$this->registerCallback('after.cancel', array($this, 'unlockResource'));

		//Set the default redirect.
        $this->setRedirect(KRequest::referrer());
    }

	/**
	 * Lock the referrer from updates
	 *
	 * @return void
	 */
	public function lockReferrer()
	{
	    KRequest::set('cookie.referrer_locked', true);
	}

	/**
	 * Unlock the referrer for updates
	 *
	 * @return void
	 */
	public function unlockReferrer()
	{
	    KRequest::set('cookie.referrer_locked', null);
	}


	/**
	 * Get the referrer
	 *
	 * @return KHttpUrl	A KHttpUrl object.
	 */
	public function getReferrer()
	{
	    $identifier = $this->getMixer()->getIdentifier();

	    $referrer = $this->getService('koowa:http.url',
	        array('url' => KRequest::get('cookie.referrer', 'url'))
	    );

	    return $referrer;
	}

	/**
	 * Set the referrer
	 *
	 * @return void
	 */
	public function setReferrer()
	{
	    $identifier = $this->getMixer()->getIdentifier();

	    if(!KRequest::has('cookie.referrer_locked'))
	    {
	        $request  = KRequest::url();
	        $referrer = KRequest::referrer();

	        //Compare request url and referrer
	        if(!isset($referrer) || ((string) $referrer == (string) $request))
	        {
	            $option = 'com_'.$identifier->package;
	            $view   = KInflector::pluralize($identifier->name);
	            $url    = 'index.php?option='.$option.'&view='.$view;

	            $referrer = $this->getService('koowa:http.url',array('url' => $url));
	        }

	        KRequest::set('cookie.referrer', (string) $referrer);
		}
	}

	/**
	 * Unset the referrer
	 *
	 * @return void
	 */
	public function unsetReferrer()
	{
	    $identifier = $this->getMixer()->getIdentifier();
	    KRequest::set('cookie.referrer', null);
	}

	/**
	 * Lock callback
	 *
	 * Only lock if the context contains a row object and the view layout is 'form'.
	 *
	 * @param 	KCommandContext		The active command context
	 * @return void
	 */
	public function lockResource(KCommandContext $context)
	{
       if($context->result instanceof KDatabaseRowInterface)
       {
	        $view = $this->getView();

	        if($view instanceof KViewTemplate)
	        {
                if($view->getLayout() == 'form' && $context->result->isLockable()) {
		            $context->result->lock();
		        }
            }
	    }
	}

	/**
	 * Unlock callback
	 *
	 * @param 	KCommandContext		The active command context
	 * @return void
	 */
	public function unlockResource(KCommandContext $context)
	{
	    if($context->result instanceof KDatabaseRowInterface && $context->result->isLockable()) {
			$context->result->unlock();
		}
	}

	/**
	 * Save action
	 *
	 * This function wraps around the edit or add action. If the model state is
	 * unique a edit action will be executed, if not unique an add action will be
	 * executed.
	 *
	 * This function also sets the redirect to the referrer.
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionSave(KCommandContext $context)
	{
		$action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
		$data   = $context->caller->execute($action, $context);

		//Create the redirect
		$this->setRedirect($this->getReferrer());

		return $data;
	}

	/**
	 * Apply action
	 *
	 * This function wraps around the edit or add action. If the model state is
	 * unique a edit action will be executed, if not unique an add action will be
	 * executed.
	 *
	 * This function also sets the redirect to the current url
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionApply(KCommandContext $context)
	{
		$action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
		$data   = $context->caller->execute($action, $context);

		//Create the redirect
		$url = $this->getReferrer();

		if ($data instanceof KDatabaseRowAbstract)
		{
            $url = clone KRequest::url();

		    if($this->getModel()->getState()->isUnique())
		    {
	            $states = $this->getModel()->getState()->getData(true);

		        foreach($states as $key => $value) {
		            $url->query[$key] = $data->get($key);
		        }
		    }
		    else $url->query[$data->getIdentityColumn()] = $data->get($data->getIdentityColumn());
        }

		$this->setRedirect($url);

		return $data;
	}

	/**
	 * Cancel action
	 *
	 * This function will unlock the row(s) and set the redirect to the referrer
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow	A row object containing the data of the cancelled object
	 */
	protected function _actionCancel(KCommandContext $context)
	{
		//Create the redirect
		$this->setRedirect($this->getReferrer());

	    $data = $context->caller->execute('read', $context);

		return $data;
	}
}