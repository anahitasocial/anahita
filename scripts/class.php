<?php 

namespace IO;

if ( !function_exists('readline') )
{
    function readline($text)
    {
        print $text;
        $f=popen("read; echo \$REPLY","r");
        $input=fgets($f,100);
        pclose($f);
        return trim($input);       
    }
}
function write($text) 
{
	if ( is_array($text) ) 
	{
		foreach($text as $key => $value) {
			$text[$key] = '  '.$key.' : '.$value;
		}
		$text = implode($text , "\n");		
	}
	print $text."\n";
}

function read($text, $options = array())  
{
    $options = array_merge(array(
            'key'     => null,
            'boolean' => false,
            'required'=> false,
            'answers' => array(),
            'default' => null
    ), $options);
    
    if ( $options['boolean'] ) 
    {
        $options['required'] = true;
        $options['answers']  = array('y'=>true,'n'=>false); 
        $text .= ' [Y/N] ';
    }

    extract($options);

    if ( $default ) {
        $text .= '('.$default.') ';
    }
    
    if ( $key && isset($_GET[$key]) ) {
        return $_GET[$key];
    }
    
    while(true) 
    {
        $break = true;
        $value = readline($text);
        $value = $value ? $value : $default;
        if ( $required && empty($value) ) {
            write('No value entered. Please enter a value ');
            $break = false;
        }
        if ( !empty($answers) ) 
        {
            if ( !isset($answers[strtolower($value)]) ) {
                write('You must enter one of the values : '.implode('/',array_keys($answers)));
                $break = false; 
            } else {
                $value = $answers[strtolower($value)];
            }
        }
        if ( $break )
            break;
    }
        
    if ( $key ) {
        $_GET[$key] = $value;
    }    
    return $value;    
}

namespace Installer;

class Mapper
{
    protected $_maps = array();
    protected $_src_root;
    protected $_target_root;
    
    public function __construct($src_root, $target_root)
    {
        $this->_src_root    = rtrim($src_root,'/');        
        $this->_target_root = rtrim($target_root,'/'); 
    }
    
    public function getMap($src, $target = null)
    {
        if ( !$target ) {
            $target = $src;
        }
        
        $src     =  $this->_src_root.'/'.ltrim($src, '/');
        $target  =  $this->_target_root.'/'.ltrim($target, '/');
        $map     = new Map($src, $target);        
        return $map;
    }
    
    public function addMap($src, $target = null)
    {        
        $this->_maps[] = $this->getMap($src,$target);
    }
    
    public function addCrawlMap($src, $patterns)
    {        
        if ( !empty($src) ) {
            $root = $this->_src_root.'/'.$src;
        } else {
            $root = $this->_src_root;
        }        
        $crawler = new Crawler($root, $patterns);
        $paths   = $crawler->getPaths();
        foreach($paths as $path) {                        
            $this->addMap($src.'/'.$path, str_replace('site/','',$path));
        }
    }
    
    public function symlink()
    {
        foreach($this->_maps as $map) {            
            $map->symlink();
        }
        $deadlinks = explode("\n", trim(`find -L {$this->_target_root} -type l -lname '*'`));
        $deadlinks = array_filter($deadlinks);
        if ( count($deadlinks) )
        {
            \IO\write('Deleting dead link :');
            foreach($deadlinks as $link) 
            {
                \IO\write(' '.$link);
                @unlink($link);
            }
        }        
    }
}

class Map
{
    protected $_src;
    protected $_target;
    
    public function __construct($src, $target)
    {
        $this->_src     = $src;
        $this->_target  = $target;
    }
    
    public function copy()
    {
        if ( file_exists($this->_target) ) {
           if ( is_link($this->_target) ) {
               unlink($this->_target);
           }
           else {
               exec("rm -rf {$this->_target}");
           }
        }
        exec("cp -r {$this->_src} {$this->_target}");
    }
    
    public function symlink()
    {
        //check if the parent directory exits
        $parts = array_filter(explode('/', $this->_target));
        $file  = array_pop($parts);
        $path  = '/'.implode('/', $parts);
        if ( !file_exists($path) ) {
            mkdir($path, 0755, true);
        }
        if ( file_exists($this->_target) )
        {
            if ( is_link($this->_target) ) {
        
            }
            elseif (is_dir($this->_target)) {
                exec("rm -rf {$this->_target}");
            }
        }
        @symlink($this->_src, $this->_target);        
    }
    
    public function __get($key)
    {
        return $this->{'_'.$key};
    }
}

class Crawler
{
    protected $_root;
    protected $_paths;    
    
    public function __construct($root, $patterns = array())
    {
        $this->_root = rtrim($root,'/');
              
        if ( !(file_exists($this->_root)) ) {
            throw new \RuntimeException("can't open the directory ".$this->_root);
        }
        
        $paths = array();    
            
        foreach($this->_crawl($this->_root) as $path) 
        {
            foreach($patterns as $pattern => $replacement) {
                $path  = preg_replace($pattern, $replacement, $path);
            }
            if ( !empty($path) ) {
                $paths[] = $path;
            }
        }

        $this->_paths = array_unique($paths);
    }

    public function getRoot()
    {
        return $this->_root;    
    }
    
    public function getPaths()
    {
        return $this->_paths;
    }
    
    protected function _crawl($root)
    {
        $root  = rtrim($root, '/');
        $dh    = opendir($root);
        $paths = array();
        
        while( false !== ( $file = readdir( $dh ) ) )
        {
            if ( strpos($file,'.') === 0   || 
                    $file == 'index.html'  ||
                    $file == 'robots.txt'
                    ) {
                continue;
            }
            $path    = $root.'/'.$file;
            if ( is_dir($path) ) {
                $paths = array_merge($paths, $this->_crawl($path));
            }
            else {
                $paths[] = str_replace($this->_root.'/', '', $path);
            }
        }
        
        return $paths;
    }
}
?>