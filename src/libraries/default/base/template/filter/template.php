<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateFilterTemplate extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterRead
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Replace template alias with loadFile functions.
     *
     * This function only replaces relative identifiers to a full path
     * based on the path of the template.
     *
     * @param string
     * @return LibBaseTemplateFilterAlias
     */
    public function read(&$text)
    {
        if (preg_match_all('#@template\(\'(.*)\'#siU', $text, $matches)) {
            foreach ($matches[1] as $key => $match) {
                if (is_string($match) && strpos($match, '.') === false) {
                    $path =  dirname($this->getTemplate()->getPath()).DS.$match.'.php';
                    $text = str_replace($matches[0][$key], '$this->loadFile('."'".$path."'", $text);
                }
            }
        }

        return $this;
    }
}
