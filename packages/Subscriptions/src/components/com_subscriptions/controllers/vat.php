<?php

/**
 * VAT Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerVat extends ComBaseControllerService
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback(array(
                                  'after.edit',
                                  'after.add', ),
                                  array($this, 'setTaxInfo'));
    }

    /**
     * Set the tax info of the entity.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    public function setTaxInfo(KCommandContext $context)
    {
        $vats = $context->data->federal_tax;
        $this->getItem()->setFederalTax(KConfig::unbox($vats));
    }
}
