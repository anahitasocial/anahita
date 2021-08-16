<?php

class ComBlogsControllerBlog extends ComBaseControllerResource
{
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'request' => array(
                'start' => 0,
                'limit' => 20,
                'sort' => 'recent',
            ),
        ));

        parent::_initialize($config);
    }
    
    protected function _actionGet($context)
    {
        $this->getService('repos:blogs.node')->addBehavior('privatable');
        
        $ownerIds = get_config_value('blogs.owner_ids');
        $ownerIds = explode(',', $ownerIds);
        
        $authorIds = get_config_value('blogs.created_by_ids');
        $authorIds = explode(',', $authorIds);
        
        $query = $this->getService('com:blogs.domain.query.node')
        ->where('node.owner_id', 'IN', $ownerIds)
        ->where('node.created_by', 'IN', $authorIds)
        ->where('node.type', 'IN', array(
            'ComMediumDomainEntityMedium,ComArticlesDomainEntityArticle,com:articles.domain.entity.article',
            'ComMediumDomainEntityMedium,ComNotesDomainEntityNote,com:notes.domain.entity.note',
            'ComMediumDomainEntityMedium,ComPhotosDomainEntityPhoto,com:photos.domain.entity.photo'
        ))
        ->limit($this->limit, $this->start);
        
        switch ($this->sort) {
            case 'updated':
                $query->order('node.modified_on', 'DESC');
            break;

            case 'recent':
            case 'newest':
                $query->order('node.created_on', 'DESC');
            break;
        }
                      
        $entities = $query->toEntitySet();

        $this->_state->setList($entities);

        parent::_actionGet($context);
    }
}