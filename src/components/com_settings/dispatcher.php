<?php

class ComSettingsDispatcher extends ComBaseDispatcherDefault
{
    protected function _actionDispatch(KCommandContext $context)
    {
        if ($this->getController()->getView()->getName() == 'settings') {
            $this->getController()->execute('read', $context);
        }

        return parent::_actionDispatch($context);
    }
}
