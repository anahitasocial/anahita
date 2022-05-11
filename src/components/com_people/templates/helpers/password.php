<?php

/**
 * Password Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleTemplateHelperPassword extends LibBaseTemplateHelperAbstract
{
    /**
     * Renders a password input with the validation.
     *
     * @param bool $required A boolean flag whether the password is
     *                       required or not
     */
    public function input($options = array())
    {
        $options = new AnConfig($options);
        $options->append(array(
            'id' => 'person-password',
            'name' => 'password',
            'class' => 'input-block-level',
            'required' => 'required',
            'minlength' => 8,
            'maxlength' => 80,
            'autocomplete' => 'new-password',
        ));

        $html = $this->getService('com:base.template.helper.html');

        return $html->passwordfield($options['name'], $options)
                    ->class($options['class'])
                    ->id($options['id'])
                    ->required($options['required'])
                    ->minlength($options['minlength'])
                    ->autocomplete($options['autocomplete']);
    }
}
