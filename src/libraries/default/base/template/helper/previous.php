<?php
/**
 * Renders the previous template of the current template path.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperPrevious extends LibBaseTemplateHelperAbstract
{
    /**
     * paths.
     *
     * @var array
     */
    protected $_paths = array();

    /**
     * Render the previous template of the current template.
     *
     * @param array $data
     *
     * @return string
     */
    public function load($data = array())
    {
        $current = $this->_template->getPath();

        if (!$current) {
            throw new LibBaseTemplateException('There are no template being rendered');
        }

        if (!isset($this->_paths[$current])) {
            $previous = null;
            $search_paths = $this->_template->getSearchPaths();
            $template = null;
            $search_path = null;
            foreach ($search_paths as $path) {
                if (strpos($current, $path) === 0) {
                    $search_path = $path;
                    $template = str_replace($path.'/', '', $current);
                    break;
                }
            }

            if ($search_path) {
                $search_paths = array_slice($search_paths, array_search($search_path, $search_paths) + 1);
                foreach ($search_paths as $path) {
                    $file = $path.'/'.$template;
                    if ($this->_template->findFile($file)) {
                        $previous = $file;
                        break;
                    }
                }
            }

            if (!$previous) {
                throw new LibBaseTemplateException("The {$current} template has no previous");
            }

            $this->_paths[$current] = $previous;
        }

        return $this->_template->loadFile($this->_paths[$current], $data);
    }
}
