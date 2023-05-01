<?php

/**
 * Open Graph View Class.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2021 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseViewOg extends LibBaseViewAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'mimetype' => 'text/html',
        ));

        parent::_initialize($config);
    }
    
    /**
     * Set a view properties.
     *
     * @param   string  The property name.
     * @param   mixed   The property value.
     */
    public function __set($property, $value)
    {
        $name = $this->getName();

        if ($property == 'item') {
            $name = $property;
        }

        if ($property == $name) {
            if (!AnInflector::isPlural($name)) {
                $this->_state->setItem($value);
            }
        }

        return parent::__set($property, $value);
    }

    /**
     * Sets the.
     *
     * @return string
     */
    public function display()
    {
        //Get the view name
        $name = $this->getName();
        
        //set the state data to the view
        $this->_data = array_merge($this->_state->toArray(), $this->_data);

        if ($item = $this->_state->getItem()) {
            $this->_data[ AnInflector::singularize($name) ] = $item;
            $this->_data['item'] = $item;
            $this->output = '<html lang="en"><head> ' . $this->_renderHead() . ' </head></html>';
        }

        return parent::display();
    }
    
    protected function _renderHead()
    {
        $item = $this->_data['item'];
        $output = '';
        $title = '';
        $description = '';
        $image = '';
        $type = 'article';
        
        if ($item->getName()) {
            $title = $this->_setTitle($item->getName());
        } else {
            $title = $item->author->name;
        }

        if ($item->isDescribable()) {
            $description = $this->_setDescription($item->getDescription());
        }
        
        $link = route($item->getURL());

        error_log($link);
        
        if ($item->isPortraitable()) {
            $image = $item->getPortraitURL('large');
        } else if ($item->isCoverable()) {
            $image = $item->getCoverURL('large');
        }

        if ($item instanceof ComActorsDomainEntityActor) {
            $type = 'profile';
        }
        
        if ($title) {
            $output .= '<title>'.$title.'</title>';
        }
        
        $output .= '<meta name="description" content="'.$description.'" />';

        if ($title) {
            $output .= '<meta property="og:title" content="'.$title.'" />';
        }
        
        if ($description) {
            $output .= '<meta property="og:description" content="'.$description.'" />';
        }
        
        if ($type) {
            $output .= '<meta property="og:type" content="'.$type.'" />';
            $output .= '<meta property="og:type" content="'.$type.'" />';
        }
        
        if ($link) {
            $output .= '<meta property="og:url" content="'.$link.'" />';
        }
        
        if ($image) {
            $output .= '<meta property="og:image" content="'.$image.'" />';
        }

        return $output;
    }
    
    protected function _setDescription($description)
    {
        $description = markdown_to_text($description);
        
        $stripURLRegex = "/((?<!=\")(http|ftp)+(s)?:\/\/[^<>()\s]+)/i";
        $description = preg_replace($stripURLRegex, '', $description);
        // $description = strip_tags($description);
        // $description = htmlentities($description);
        $description = str_replace(array('#', '@'), '', $description);
        $description = AnService::get('com:base.template.helper.text')->truncate($description, array('length' => 160));
        $description = trim($description);

        return $description;
	}
    
    protected function _setTitle($title)
    {
        $title = str_replace(array('#', '@'), '', $title);
        return $title;
	}
}

function clean($string) {
   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}