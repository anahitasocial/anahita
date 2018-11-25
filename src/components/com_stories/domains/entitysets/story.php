<?php

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComStoriesDomainEntitysetStory extends AnDomainEntitysetDefault
{
    /*
    * Because the number of stories can get very large, 
    * we just return a small integer. The Client can send requests 
    * and hope that more stories are returned. 
    */
    const MAX_TOTAL_STORIES = 20;
    
    /**
     * Return 
     *
     * @return int
     */
    public function getTotal()
    {
        return ComStoriesDomainEntitysetStory::MAX_TOTAL_STORIES;
    }
}