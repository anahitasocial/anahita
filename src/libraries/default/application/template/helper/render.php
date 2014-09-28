<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Rendering script
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibApplicationTemplateHelperRender extends KTemplateHelperAbstract
{    
    /**
     * Template parameters
     * 
     * @return KConfig
     */ 
    protected $_params;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_params = $this->_template->getView()->getParams();
    }
        
    /**
     * Renders the logo hyperlinked
     * 
     * @param $config Configuration
     * 
     * @return string
     */
    public function logo($config = array())
    {
        $config = new KConfig($config);
        
        $config->append(array(
            'show_logo' => pick($this->_params->showLogo, 1),
            'name' => pick($this->_params->brandName, 'Anahita'),
            'url' => 'base://'
        ));
        
        $showLogo = ($config->show_logo) ? ' brand-logo' : '';
        
        return '<a class="brand'.$showLogo.'" href="'.$config->url.'">'.$config->name.'</a>';
    }
    
	/**
     * Renders the favicon tag
     * 
     * @param $config Configuration
     * 
     * @return string
     */
    public function favicon($config = array())
    {
        $config = new KConfig($config);
        
        $config->append(array(
            'favicon' => pick($this->_params->favicon, 'favicon.ico'),
        	'type' => 'image/png',
            'url' => 'base://'
        ));
        
        $paths = array(
            JPATH_THEMES.DS.'base'.DS.'css'.DS.'images',
            JPATH_THEMES.DS.$this->getIdentifier()->package.DS.'css'.DS.'images'
        );
        
        $finder = $this->getService('anahita:file.pathfinder');
        
        $finder->addSearchDirs($paths);
        
        $path = str_replace('\\', '/', str_replace(JPATH_ROOT.DS, 'base://', $finder->getPath('favicon.ico')));
        
        return '<link rel="icon" type="'.$config->type.'" href="'.$path.'" />';
    }
    
    /**
     * Renders the template style
     * 
     * @param array  $config Configuration 
     * 
     * @return string
     */
    public function style($config = array())
    {
        require_once 'less/compiler.php';
        
        $config = new KConfig($config);
        
        $config->append(array(
            'parse_urls' => true,
            'style' => pick($this->_params->cssStyle, 'style1'),
            'compile' => pick($this->_params->compilestyle, 0),
            'compress' => pick($this->_params->compresstyle, 1),
        ));

        $paths = array(
            JPATH_ROOT.DS.'media'.DS.'lib_anahita'.DS.'css',
            JPATH_THEMES.DS.'base'.DS.'css',
            $css_folder = JPATH_ROOT.DS.'templates'.DS.$this->getIdentifier()->package.DS.'css'.DS.$config->style
        );
        
        $finder = $this->getService('anahita:file.pathfinder');
        $finder->addSearchDirs($paths);
        $style = $finder->getPath('style.less');
        $css = $css_folder.DS.'style.css';
        
        //compile        
        if($config->compile > 0 && !empty($style))
        {
            $this->_template->renderHelper('less.compile', array(
                'force' => $config->compile > 1,
                'compress' => $config->compress,
                'parse_urls' => $config->parse_urls,
                'import' => $finder->getSearchDirs(),
                'input' => $style,
                'output' => $css
            ));
        }
        
        $cssHref = str_replace('\\', '/', str_replace(JPATH_ROOT.DS, 'base://', $css));
        return '<link rel="stylesheet" href="'.$cssHref.'" type="text/css" />';
    }
    
	/**
     * Render the document queued messages
     * 
     * @return string
     */
    public function messages()
    {        
    	$session =& JFactory::getSession();
        $queue = (array) $session->get('application.queue', array());
        
        $session->set('application.queue', null);
        
        if(isset($queue['message'])) 
        {
        	$message = $queue['message'];
            $config  = array('closable'=>true);
            
            if(isset($message['type']))
                $config['type'] = $message['type'];
            
            return $this->getTemplate()->renderHelper('ui.message', $message['message'], $config);
        }
        
        return '';
    }
    
    /**
     * Render a google anayltic 
     * 
     * @param array $config Configuration
     * 
     * @return string
     */
    public function analytics($config = array())
    {         
        $config = new KConfig($config);
        
        $config->append(array(
            'gid' => $this->_params->analytics
        ));
        
        $gid = $config->gid;
        
        if ( !empty($gid) )  
        return <<<EOF
<script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '$gid']);
          _gaq.push(['_trackPageview']);
        
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>        
EOF;
        
    }        
}