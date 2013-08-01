<?php
define("SOURCE", '/Users/asanieyan/Sites/anahitapolis/anahita');
define("TARGET", '/Users/asanieyan/Sites/anahitapolis/master/anahita_src');
$anahita_base = 'src';
$rm_r   = function($target)
{ 
    $target = TARGET .'/'.$target;
    exec("rm -rf $target");
};
$copy_r = function($src, $target)
{
    $src    = SOURCE.'/'.$src;
    if ( !file_exists($src) ) {
        return;
    }
    if ( is_dir($src) ) {
        $src = $src.'/*';
    }
    $target = TARGET.'/'.$target;
    $dir    = $target;
    if ( strpos(basename($dir), '.') ) {
        $dir = dirname($dir);
    }
    @mkdir(strtolower($dir), 0755, true);
    exec("cp -r $src $target");
};

$rm_r($anahita_base);
$copy_r('src/administrator/includes', $anahita_base.'/application/administrator/includes');
$copy_r('src/site/includes', $anahita_base.'/application/site/includes');
$copy_r('src/libraries/anahita', $anahita_base.'/application/libraries/anahita');

foreach(array('src/site/components','src/administrator/components','src/libraries/default')
            as $path
        )
{
    $dirs = new DirectoryIterator(SOURCE.'/'.$path);
    foreach($dirs as $dir)
    {
        if ( $dir->isDot() || $dir->isFile() ) 
                continue;
        $component = ucfirst(str_replace('com_', '', $dir));
        $component = strtolower($component);
        if ( strpos($dir->getPathName(), 'site') ) {
            $app = 'site';
        } elseif (strpos($dir->getPathName(), 'admin') ) {
            $app = 'admin';
        } else {
            $app = 'component';
        }
        $source = $path.'/'.$dir;
        $target = $anahita_base."/components/$component/$app";        
        $copy_r($source, $target);
    }
}
 
foreach(array('src/site/language/en-GB','src/administrator/language/en-GB')
        as $path
)
{
    $dirs = new DirectoryIterator(SOURCE.'/'.$path);
    $app  = strpos($path, 'admin') ? 'admin' : 'site';
    foreach($dirs as $dir)
    {
        if ( $dir->isFile() && !$dir->isDot() )
        {
            $file    = (string)$dir;
            $matches = array();
            if ( preg_match('/en-GB\.com_(\w+)\.ini/', $file, $matches) )
            {
                $component = ucfirst($matches[1]);
                $component = strtolower($component);
                $target    = $anahita_base."/components/$component/$app/resources/language";
                $copy_r($path.'/'.$file, $target.'/en-GB.ini');                
            }
        }
    }    
}
$copy_r('src/media/com_composer',$anahita_base.'/components/composer/site/resources/media');
$copy_r('src/media/com_stories',$anahita_base.'/components/stories/site/resources/media');
$copy_r('src/media/com_search',$anahita_base.'/components/search/site/resources/media');
$copy_r('src/media/lib_anahita/css',$anahita_base.'/components/bootstrap/component/resources/media/css');
$copy_r('src/media/lib_anahita/images',$anahita_base.'/components/bootstrap/component/resources/media/images');
$copy_r('src/media/lib_anahita/js',$anahita_base.'/components/application/site/resources/media/js');
$copy_r('src/site/templates/base/css'  ,$anahita_base.'/components/application/site/resources/media/css');
$copy_r('src/site/templates/base/html'  ,$anahita_base.'/components/application/site/theme/html');
$copy_r('src/site/templates/shiraz',$anahita_base.'/components/shiraz/site/theme');
$copy_r('src/site/templates/shiraz/css',$anahita_base.'/components/shiraz/site/resources/media/css');
$rm_r($anahita_base.'/components/shiraz/site/theme/css');
$copy_r('src/site/language/en-GB/en-GB.tpl_shiraz.ini',$anahita_base.'/components/shiraz/site/resources/language');
$copy_r('src/site/language/en-GB/en-GB.lib_anahita.ini',$anahita_base.'/components/application/site/resources/language');
$copy_r('src/site/language/en-GB/en-GB.lib_anahita.js',$anahita_base.'/components/application/site/resources/language');
$copy_r('src/plugins',$anahita_base.'/components/application/plugins');
$copy_r('src/site/modules/mod_base',$anahita_base.'/components/base/site/modules/base');
$copy_r('src/site/modules/mod_menu',$anahita_base.'/components/menu/site/modules/menu');
$copy_r('src/site/modules/mod_search',$anahita_base.'/components/search/site/modules/search');
$copy_r('src/site/modules/mod_viewer',$anahita_base.'/components/people/site/modules/viewer');

//$comp_path = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/packages2';
$package_path = 'packages';
$rm_r($package_path);
$copy_component = function($name) use ($copy_r, $package_path)
{  
    $source = 'packages/'.ucfirst($name).'/src';
    $target = $package_path.'/'.$name.'/src';
    $copy_r("$source/administrator/components/com_$name", "$target/admin");
    $copy_r("$source/administrator/language/en-GB/en-GB.com_$name.ini", "$target/admin/resources/language/en-GB.ini");
    $copy_r("$source/site/components/com_$name", "$target/site");
    $copy_r("$source/site/language/en-GB/en-GB.com_$name.ini", "$target/site/resources/language/en-GB.ini");
    $copy_r("$source/media/com_$name", "$target/site/resources/media");
    $copy_r("$source/plugins", "$target/plugins");
    $copy_r("$source/../composer.json", "$target/../");    
};
$componets = explode(" ",'photos pages topics todos html autofollow connect groups invites opensocial subscriptions');
foreach($componets as $component) {
    $copy_component($component);
}

//exec('patch --dry-run -p1 < patchfile.patch');
//patch -p1 < patchfile.patch

