<?php
/**
 * @version		$Id$
 * @category	Anahita
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */


print ComBaseDispatcher::getInstance()->dispatch(KRequest::get('get.view','cmd', 'notifications'));