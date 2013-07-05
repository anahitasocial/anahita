<?php 

namespace Console\Extension;

/**
 * Package aggregator.
 * 
 * Provides method that can be applied to a set of package
 * 
 */
class Packages extends \ArrayObject
{    
    /**
     * Return a set of packages by name. The name can follow the pattern
     * vendor/* => return all the packages with vendor
     * name     => return all the packages with name 
     * vendor/name => return the package that matches the vendor/name 
     * 
     * @param array|string $names
     * 
     * @return boolean|\Console\Extension\Packages
     */
    public function findPackages($names)
    {
        settype($name, 'array');
        $packages = array();
        foreach($names as $name) 
        {            
            $name = trim($name);
            $matches = array();
            if ( preg_match('/^(\w+)\/\*$/', $name, $matches) ) 
            {
                $array    = array_filter($this->getArrayCopy(), 
                function($package) use ($matches) {
                    return $package->getVendor() == $matches[1];
                });
                $packages = array_merge($packages, $array);
            }
            elseif ( preg_match('/^\w+$/', $name, $matches) ) {
                $array    = array_filter($this->getArrayCopy(), 
                function($package) use ($matches) {
                    return $package->getName() == $matches[0];
                });
                $packages = array_merge($packages, $array);
            }
            elseif ( preg_match('/^\w+\/\w+$/', $name, $matches) ) {
                $array    = array_filter($this->getArrayCopy(),
                        function($package) use ($matches) {
                            return $package->getFullName() == $matches[0];
                        });
                $packages = array_merge($packages, $array);
            }            
        }
        $packages = new self($packages);
        return $packages;
    }
    
    /**
     * Appens only packages
     * 
     * @param string $key
     * @param Package $package
     * 
     * @return void
     */
    public function offsetset($key, $package)
    {
        $key = $package->getVendor().'/'.$package->getName();
        parent::offsetSet($key, $package); 
    }
    
    /**
     * Adds packages using the a set of composer files
     * 
     * @param string $composers
     * 
     * @return void
     */
    public function addPackageFromComposerFiles($composers)
    {
        settype($composers, 'array');
        foreach($composers as $file) 
        {
            $data = (array)json_decode(file_get_contents($file));
            
            if ( isset($data['type']) && 
                        $data['type'] == 'anahita-extension') 
            {
                if ( !isset($data['extension-source']) ) {
                    $data['extension-source'] = 'src'; 
                }
                
                $data['extension-source'] = realpath(dirname($file).'/src');
                                
                if ( is_readable($data['extension-source']) ) 
                {
                    $parts   = array_filter(explode('/', @$data['name']));                    
                    $name    = strtolower(pick(@$parts[1], basename(dirname($file))));
                    $vendor  = strtolower(pick(@$parts[0], $name));                    
                    $this[]  = new Package(array(
                        'composer_file' => $file,
                        'name'          => $name,
                        'vendor'        => $vendor,
                        'source'        => $data['extension-source']      
                    ));                    
                }
            }
        }
    }
}