<?php 

$copy_r = function($src, $target)
{
    $src    = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/packages/'.$src;
    $target = '/Users/asanieyan/Sites/anahitapolis/master/anahita_src/packages2/'.$target;
    $dir    = $target;
    if ( strpos(basename($dir), '.') ) {
        $dir = dirname($dir);
    }
    if ( file_exists($src) )
    {
        if ( is_dir($src) ) {
            $src = $src.'/*';
        }        
        @mkdir(strtolower($dir), 0755, true);
        $target = strtolower($target);        
        exec("cp -r $src $target");
    }
};

$copy_component = function($name) use ($copy_r)
{
    exec("rm -rf /Users/asanieyan/Sites/anahitapolis/master/anahita_src/packages2/$name");
    $source = ucfirst($name).'/src';
    $target = ucfirst($name).'/src';
    $copy_r("$source/administrator/components/com_$name", "$target/admin");
    $copy_r("$source/administrator/language/en-GB/en-GB.com_$name.ini", "$target/admin/resources/language/en-GB.ini");
    
    $copy_r("$source/site/components/com_$name", "$target/site");
    $copy_r("$source/site/language/en-GB/en-GB.com_$name.ini", "$target/site/resources/language/en-GB.ini");
    $copy_r("$source/media/com_$name", "$target/site/resources/media");
    $copy_r("$source/plugins", "$target/plugins");
    $copy_r("$name/composer.json", "$name");
};
$componets = explode(" ",'photos pages topics');
foreach($componets as $component) {
    $copy_component($component);
}
?>