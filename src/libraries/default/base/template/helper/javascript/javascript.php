<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Javascript Helper.
 *
 * NOTE Expermimental and subject to change
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperJavascript extends LibBaseTemplateHelperAbstract
{
    /**
     * Compress a javascript file and put the output in the.
     *
     * @param array $config Configuration.
     *                      $files   The file to compress
     *                      $output The output file
     */
    public function combine($config = array())
    {
        $this->getService('com:base.template.helper.javascript.file', $config)->write($config['output']);
    }

    /**
     * Loads an aray of javascript language.
     *
     * @params array $langs Array of language files
     */
    public function language($langs)
    {
        //force array
        settype($langs, 'array');

        $scripts = '';
        $language = $this->getService('anahita:language');
        $tag = $language->getTag();
        $base = $language->getLanguagePath(ANPATH_ROOT, $tag);

        foreach ($langs as $lang) {
            $path = $base.'/'.$tag.'.'.$lang.'.js';
            if (is_readable($path)) {
                $src = KService::get('com:application')->getRouter()->getBaseUrl().'/language/'.$tag.'/'.$tag.'.'.$lang.'.js';
                $scripts .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
            }
        }

        return  $scripts;
    }
}
