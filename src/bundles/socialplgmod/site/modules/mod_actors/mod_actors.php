<?php defined('KOOWA') or die('Restricted access');

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Actors
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

$actor_ids = trim($params->get('actor_ids', ''));

if ( !empty($actor_ids) )
{
	$actor_ids = explode(',', $actor_ids);
	$sort 	   = $params->get('sort', 'status_update');
	$query     = KService::get('repos:actors.actor')
				 ->getQuery()
				 ->disableChain()
				 ->where('id', 'IN', $actor_ids);
				
	if (!empty($actor_ids)) 
	{
		if ( $sort == 'status_update')
			$query->order('statusUpdateTime', 'DESC');
		elseif ( $sort == 'title' )
			$query->order('name', 'DESC');
		elseif ( $sort == 'create_date' )
			$query->order('creationTime', 'DESC');	
		else if ( $sort == 'order' ) 
		{
			$whens = array();
			
			foreach($actor_ids as $index => $id)
				$whens[] = "WHEN id = $id THEN $index";

			$select = "CASE ".implode($whens, ' ').' ELSE 1 END AS entered_order';	
			$query->select($select);
			$query->order('entered_order', 'ASC');	
	     }
	
		$actors = $query->fetchSet();
		
		if ( count($actors) > 0)
	    	print KService::get('mod://site/actors.html')
	    	        ->header_text($params->get('header_text'))
	    	        ->actor_layout($params->get('layout', 'grid'))
	    	        ->actors($actors);		
	}
}