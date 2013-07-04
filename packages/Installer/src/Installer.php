<?php 
namespace Console\Composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class AnahitaInstaller extends LibraryInstaller
{
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        print 'd';
        die;
        $prefix = substr($package->getPrettyName(), 0, 23);
        if ('phpdocumentor/template-' !== $prefix) {
            throw new \InvalidArgumentException(
                    'Unable to install template, phpdocumentor templates '
                    .'should always start their package name with '
                    .'"phpdocumentor/template-"'
            );
        }

        return 'data/templates/'.substr($package->getPrettyName(), 23);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        print $packageType;
        die;
        return 'phpdocumentor-template' === $packageType;
    }
}

?>