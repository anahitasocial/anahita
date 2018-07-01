<?php

class ComSettingsDispatcher extends ComBaseDispatcherDefault
{
    protected function _actionDispatch(AnCommandContext $context)
    {
        if ($this->getController()->getView()->getName() == 'settings') {
            $this->getController()->execute('read', $context);
        }

        return parent::_actionDispatch($context);
    }
}
