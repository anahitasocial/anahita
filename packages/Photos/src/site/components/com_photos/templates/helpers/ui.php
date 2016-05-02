<?php
/**
 * Photo Helper.
 *
 * Provides methods for rendering ui elements for photos app
 *
 * @category   Photos
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

class ComPhotosTemplateHelperUi extends ComBaseTemplateHelperUi
{
    public function uploadLimit($config = array())
    {
        $config = (object) $config;
        $uploadMaxFilesize = (int) ini_get('upload_max_filesize');
        $postMaxSize = (int) ini_get('post_max_size');

        if ($postMaxSize > 10) {
            $postMaxSize = 10;
        }

        $limit = ($uploadMaxFilesize < $postMaxSize) ? $uploadMaxFilesize : $postMaxSize;

        $options = array();

        for ($i = 1; $i < $limit + 1; ++$i) {
            $options[] = array('name' => $i, 'value' => $i);
        }

        $html = '<select name="'.$config->name.'" id="'.$config->id.'" class="'.$config->class.'" >'."\n";

        foreach($options as $option){
            $selected = ($option['value'] == $config->value) ? 'selected' : '';
            $html .= '<option '.$selected.' value="'.$option['value'].'">'.$option['name'].' MB</option>'."\n";
        }

        $html .= '</select>'."\n";

        return $html;
    }
}
