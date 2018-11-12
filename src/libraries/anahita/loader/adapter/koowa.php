<?php

class AnLoaderAdapterKoowa extends AnLoaderAdapterAbstract
{
    /** 
     * The adapter type.
     * 
     * @var string
     */
    protected $_type = 'koowa';

    /**
     * The class prefix.
     * 
     * @var string
     */
    protected $_prefix = 'K';

    /**
     * Get the path based on a class name.
     *
     * @param  string		  	The class name 
     *
     * @return string|false Returns the path on success FALSE on failure
     */
    public function findPath($classname, $basepath = null)
    {
        $path = false;

        $word = preg_replace('/(?<=\\w)([A-Z])/', ' \\1',  $classname);
        $parts = explode(' ', $word);

        // If class start with a 'An' it is a anahita framework class and we handle it
        if (array_shift($parts) == $this->_prefix) {
            $path = strtolower(implode('/', $parts));

            if (count($parts) == 1) {
                $path = $path.'/'.$path;
            }

            if (!is_file($this->_basepath.'/'.$path.'.php')) {
                $path = $path.'/'.strtolower(array_pop($parts));
            }

            $path = $this->_basepath.'/'.$path.'.php';
        }

        return $path;
    }
}
