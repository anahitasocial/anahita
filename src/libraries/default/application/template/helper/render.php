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
            'name'      => pick($this->_params->brandName, 'Anahita'),
            'url'       => 'base://'
        ));
        
        $showLogo = ( $config->show_logo ) ? ' brand-logo' : '';
        
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
            'favicon'	=> pick($this->_params->favicon, 'favicon.ico'),
        	'type'		=> 'image/png',
            'url'       => 'base://'
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
            'parse_urls'   => true,
            'style'        => pick($this->_params->cssStyle, 'style1'),
            'compile'      => pick($this->_params->compilestyle, 0),
            'compress'     => pick($this->_params->compresstyle, 1),
        ));

        $paths = array(
            JPATH_ROOT.DS.'media'.DS.'lib_anahita'.DS.'css',
            JPATH_THEMES.DS.'base'.DS.'css',
            $css_folder = JPATH_ROOT.DS.'templates'.DS.$this->getIdentifier()->package.DS.'css'.DS.$config->style
        );
        
        $finder = $this->getService('anahita:file.pathfinder');
        $finder->addSearchDirs($paths);
        $style = $finder->getPath('style.less');
        $css   = $css_folder.DS.'style.css';
        //compile        
        if ( $config->compile > 0 && !empty($style) )
        {
            $this->_template->renderHelper('less.compile', array(
                'force'      => $config->compile > 1,
                'compress'   => $config->compress,
                'parse_urls' => $config->parse_urls,
                'import'     => $finder->getSearchDirs(),
                'input'      => $style,
                'output'     => $css
            ));
        }
        
        $cssHref = str_replace('\\', '/', str_replace(JPATH_ROOT.DS, 'base://', $css));
        return '<link rel="stylesheet" href="'.$cssHref.'" type="text/css" />';
    }
    
    /**
     * Renders a row of modules
     * 
     * @param string $row    The module row-position  
     * @param array  $config Configuration 
     * 
     * @return string
     */
    public function modules($row, $config = array())
    {
        $config = new KConfig($config);
        
        $config->append(array(           
            'style'  => 'default',
            'spans'  => pick($this->_params->{$row},'4,4,4,4'),
        ));
        
        if ( is_string($config->spans) ) {
            $config->spans = explode(',', $config->spans);    
        }
                
        $html           = '';
        foreach($config->spans as $i => $span)
        {
            $position = $row.'-'.chr($i + ord('a'));
            $modules  = JModuleHelper::getModules($position);
            if ( count($modules) )
            {
                $column   = $this->_template->getHelper('modules')->render($modules, KConfig::unbox($config));
                if ( !empty($column) ) {                     
                    $html .=  '<div class="span'.$span.'">'.$column.'</div>'; 
                }
            }
        }
        
        if ( !empty($html) ) {
            $html = '<div class="container" id="container-'.$row.'"><div class="row" id="row-'.$row.'">'.$html.'</div></div>';
        }
                    
        return $html;
    }
        
    /**
     * Render a component
     * 
     * @param array $config Configuration 
     * 
     * @return string
     */
    public function component($config = array())
    {   
        $modules    = $this->_template->getHelper('modules');
        $config     = new KConfig($config);
        
        //if no content then get the content from the view
        if ( !isset($config->content) ) {
            $config['content'] = $modules->render('toolbar').$this->_template->getView()->content;             
        }

        $sb_a_modules = array(); $sb_b_modules = array();

        if ( $config['render_sides'] !== false ) {
        	$sb_a_modules = JModuleHelper::getModules('sidebar-a');
        	$sb_b_modules = JModuleHelper::getModules('sidebar-b');
        }
        
        if ( !empty($sb_a_modules) ) 
        {
            //set the default span for sidebar-a 
            //sidebar-a is 2 
            $default = 2;
            
            //try to get the span from the injected module            
            if ( isset($sb_a_modules[0]->attribs) && isset($sb_a_modules[0]->attribs['span'])) {
                $default = $sb_a_modules[0]->attribs['span'];
            }
  
            $config->append(array(
                'sidebar-a' => $default,                   
            ));
        }
        
        if ( !empty($sb_b_modules) ) 
        {
            //set the default span for sidebar-b 
            //sidebar-b is 4           
            $default = 4;
            
            //try to get the span from the injected module
            if ( isset($sb_b_modules[0]->attribs) && isset($sb_b_modules[0]->attribs['span'])) {
                $default = $sb_b_modules[0]->attribs['span'];
            }
                        
            $config->append(array(
                'sidebar-b' => $default,
            ));
        }
        
        $config->append(array(
            'sidebar-a' => 0,
            'sidebar-b' => 0         
        ));
        
        $content = $config->content;
        
        $config->append(array(
            'main' => max(0, 12 - $config['sidebar-a'] - $config['sidebar-b']) 
        ));
        
        $config->append(array(
            'toolbar' => $config->main                     
        ));
                
        $html   = '';
        
        
        if ( $config['sidebar-a'] > 0 ) {
            $html .= '<div class="span'.$config['sidebar-a'].'" id="sidebar-a">'.$modules->render($sb_a_modules).'&nbsp;</div>';    
        }
        
        if ( $config['main'] > 0 && !empty($content) ) {
            $html .= '<div class="span'.$config['main'].'" id="main"><div class="block">'.$content.'</div></div>';    
        }

        if ( $config['sidebar-b'] > 0 ) {        
            $html .= '<div class="span'.$config['sidebar-b'].'" id="sidebar-b">'.$modules->render($sb_b_modules).'&nbsp;</div>';    
        }
        
        if ( !empty($html) ) {
            $html = '<div class="container" id="container-main"><div class="row" id="row-main">'.$html.'</div></div>';
        }
                
        return $html;
    }
        
    /**
     * Render the document queued messages
     * 
     * @return string
     */
    public function messages()
    {        
        $queue = (array)JFactory::getApplication()->getMessageQueue();
        
        //if there are no message then render nothing
        if ( !count($queue) )
            return '';
              
        // Get the message queue        
        $messages = array();
        
        //make messages unique
        foreach($queue as $message) 
        {
            //if message is an array
            if ( isset($message['message']) && is_array($message['message']) ) {
                $message = array_merge(array('type'=>'info'), $message['message']);
            }
            
            //make sure to not have duplicate messages
            if (isset($message['type']) && isset($message['message'])) {
                $messages[md5($message['message'])] = $message;
            }
        }
        
        $html = '';
        
        foreach($messages as $message) {
            $html .= $this->_template->getHelper('message')->render($message);
            break;
        }
        
        return $html;
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