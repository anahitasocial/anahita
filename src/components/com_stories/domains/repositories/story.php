<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Story Repository.
 *   
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesDomainRepositoryStory extends AnDomainRepositoryDefault
{
    /**
     * Creates a new story.
     * 
     * @param array $data The story data
     * 
     * @return ComStoriesDomainEntityStory
     */
    public function create($data)
    {
        $data = new AnConfig($data);

        $data->append(array(
            'owner' => $data->target ? $data->target : $data->subject,
        ));

        $data = AnConfig::unbox($data);

        return $this->getEntity(array('data' => $data));
    }

    /**
     * Before fetch.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeRepositoryFetch(AnCommandContext $context)
    {
        $query = $context->query;
        //$query->where('@col(comment.id) IS NULL');
        $query->where('IF(@col(target.enabled) IS NULL, 1, @col(target.enabled) <> 0) AND IF(@col(subject.enabled) IS NULL, 1, @col(subject.enabled) <> 0)')
              ->link('target', array('type' => 'weak', 'bind_type' => false));

        //apply the privacy 
        $privtable = $this->getBehavior('com://site/medium.domain.behavior.privatable');

        $query->privacy = pick($query->privacy, new AnConfig());

        //weak link  the stories with object nodes
        //and use the object.access instead of the story access if there are ny
        $query->link('object', array('type' => 'weak', 'bind_type' => false));
        //we are using the object.access as the privacy reference
        $query->privacy->append(array(
             'use_access_column' => '@col(object.access)',
        ));

        $privtable->execute('before.fetch', $context);
    }
}
