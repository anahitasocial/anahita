<?php

/**
 * Appable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerBehaviorAppable extends AnControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback(
                array('after.addApp', 'after.removeApp'),
                array($this, 'fetchApp')
        );
    }
    
    protected function _actionGetApps(AnCommandContext $context)
    {
        $apps = array();
        $components = $this->getService('com:actors.domain.entityset.component', array(
                'actor' => $this->getItem(),
                'can_enable' => true,
            ));
            
        foreach ($components as $component) {
            $apps[] = array(
                'id' => $component->option,
                'name' => $component->getProfileName(),
                'description' => $component->getProfileDescription(),
                'enabled' => $component->enabledForActor($this->getItem()),
            );
        }    
        
        $content = $this->getView()
        ->set('data', $apps)
        ->set('pagination', array(
            'offset' => 0,
            'limit' => 20,
            'total' => count($apps),
        ))
        ->layout('apps')
        ->display();
        
        $context->response->setContent($content);
    }
    
    /**
     * Add App.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionAddApp(AnCommandContext $context)
    {
        $data = $context->data;
        $this->getItem()->components->insert($data->app);
    }

    /**
     * Remove App.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionRemoveApp(AnCommandContext $context)
    {
        $data = $context->data;
        $this->getItem()->components->extract($data->app);
    }
    
    public function fetchApp(AnCommandContext $context) 
    {
        $data = $context->data;
        
        $component = $this->getService('repos:components.component')->fetch(array(
            'component' => $data->app
        ));
        
        $content = json_encode(array(
            'id' => $component->option,
            'name' => $component->getProfileName(),
            'description' => $component->getProfileDescription(),
            'enabled' => $component->enabledForActor($this->getItem()),
        ));
        
        $context->response->setContent($content); 
    }
    
    public function canGetapps()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}