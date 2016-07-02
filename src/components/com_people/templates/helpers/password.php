<?php

/**
 * Password Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleTemplateHelperPassword extends KTemplateHelperAbstract
{
    /**
     * Renders a password input with the validation.
     *
     * @param bool $required A boolean flag whether the password is
     *                       required or not
     */
    public function input($options = array())
    {
        $options = new KConfig($options);
        $options->append(array(
            'id' => 'person-password',
            'name' => 'password',
            'class' => 'input-block-level',
            'required' => 'required',
            'minlength' => ComPeopleFilterPassword::$MIN_LENGTH,
        ));

        $html = $this->getService('com:base.template.helper.html');

        return $html->passwordfield($options['name'], $options)
                    ->class($options['class'])
                    ->id($options['id'])
                    ->required($options['required'])
                    ->minlength($options['minlength']);
    }
}
