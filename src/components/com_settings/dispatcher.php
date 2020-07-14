<?php

class ComSettingsDispatcher extends ComBaseDispatcherDefault
{
    protected function _actionDispatch(AnCommandContext $context)
    {
        if ($this->getController()->getView()->getName() == 'settings') {
            $this->setController('config')
            ->getController()
            ->setView('config');
        }
        
        return parent::_actionDispatch($context);
    }
}
