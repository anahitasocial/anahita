<?php

CONST LIMIT = 100;

class PlgProfileContact extends PlgProfileAbstract
{
    private $_fields = array(
        'contact_url' => 'url',
        'contact_email' => 'email',
        'phone' => 'string',
        'website' => 'url',
        'facebook' => 'string',
        'instagram' => 'string',
        'twitter' => 'string',
        'linkedin' => 'string',
    );
    
    public function onSave(AnEvent $event)
	{
        if ($this->_isAccount()) {
            return;
        }
        
        $actor = $event->actor;

        foreach ($this->_fields as $key => $filter) {
            $value = AnRequest::get('post.' . $key, $filter, '');
            $actor->setValue($key, mb_substr($value, 0, LIMIT));
        }
        
        $actor->save();
        
        return;
    }
    
    public function onDisplay(AnEvent $event) 
    {
        $actor = $event->actor;
        $data = array();
        
        foreach($this->_fields as $key => $filter) {
            $data[$key] = $actor->getValue($key);
        };
                
        $event->profile->append($data);
        
        return;
    }
        
    public function onEdit(AnEvent $event) 
    {
        return;
    }   
    
    private function _isAccount()
    {
        return isset($_POST['password']) || isset($_POST['email']) || isset($_POST['username']); 
    } 
}