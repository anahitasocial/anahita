<?php 

$dest = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/Core/components';
$copy = function($path, $app) use ($dest) {
    $dirs = new DirectoryIterator($path);
    foreach($dirs as $dir)
    {
        if ( $dir->isDot() || $dir->isFile() ) continue;    
        $component = ucfirst(str_replace('com_', '', $dir));
        $component = strtolower($component);
        $source =  $dir->getPathName();
        $target = "$dest/$component/$app";
        @mkdir($target, 0755, true);
        exec("cp -r $source/* $target");
    }    
};

$copy('/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/site/components', 'site');
$copy('/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/administrator/components', 'admin');
$copy('/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/libraries/default', 'component');

$lang = '';
$copy = function($path, $app) use ($dest) 
{
    $dirs = new DirectoryIterator($path);
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
                $target    = "$dest/$component/$app/resources/language";
                @mkdir($target, 0755, true);
                exec("cp -r {$dir->getPathName()} $target/en-GB.ini");
            }
        }
    }
};

$copy('/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/administrator/language/en-GB', 'admin');
$copy('/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/site/language/en-GB', 'site');

$copy_r = function($src, $target) 
{
    $src    = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/src/'.$src;
    if ( is_dir($src) ) {
        $src = $src.'/*';
    }
    $target = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/Core'.'/'.$target;
    $dir    = $target;
    if ( strpos(basename($dir), '.') ) {
        $dir = dirname($dir);
    }
    @mkdir(strtolower($dir), 0755, true);
    $target = strtolower($target);
    exec("cp -r $src $target");
};

$copy_r('media/com_composer','Components/Composer/site/resources/media');
$copy_r('media/com_stories','Components/Stories/site/resources/media');
$copy_r('media/com_search','Components/Search/site/resources/media');
$copy_r('media/lib_anahita','Components/Application/site/resources/media');
$copy_r('site/templates/base'  ,'Components/Application/site/theme');
$copy_r('site/templates/shiraz','Components/Shiraz/site/theme');
$copy_r('site/language/en-GB/en-GB.tpl_shiraz.ini','Components/Shiraz/site/resources/language');
$copy_r('site/language/en-GB/en-GB.tpl_shiraz.ini','Components/Shiraz/site/resources/language');
$copy_r('plugins','Components/Application/plugins');
$copy_r('site/modules/mod_base','Components/Base/site/modules/base');
$copy_r('site/modules/mod_menu','Components/Menu/site/modules/menu');
$copy_r('site/modules/mod_menu','Components/Search/site/modules/search');
$copy_r('site/modules/mod_viewer','Components/Base/site/modules/viewer');
?>