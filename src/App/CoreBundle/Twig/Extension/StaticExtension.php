<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 26.02.14
 * Time: 14:25
 */

namespace App\CoreBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;

class StaticExtension extends \Twig_Extension
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct(KernelInterface $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * {@inherit-Doc}
     */
    public function getFunctions()
    {
        return array(
            'file' => new \Twig_Function_Method($this, 'file', ['is_safe' => ['html']])
        );
    }

    /**
     * Returns the contents of a file to the template.
     *
     * @param string $path A logical path to the file (e.g '@AcmeFooBundle:Foo:resource.txt').
     *
     * @return string The contents of a file.
     */
    public function file($path)
    {
        if(count($path) > 0 && $path[0] == '@'){
            $path = $this->kernel->locateResource($path, null, true);
        }

        return file_get_contents($path);
    }

    public function getName()
    {
        return 'static';
    }
}