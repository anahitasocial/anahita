<?php

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesDomainEntitysetStory extends AnDomainEntitysetDefault
{
    /*
    * Because the number of stories can get very large, 
    * we just return a not so very large integer. 
    * It's unlikely that people would request more than a few hundred stories.
    *  
    */
    const MAX_TOTAL_STORIES = 777;
    
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