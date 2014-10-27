<?php
// src/Acme/DemoBundle/DependencyInjection/Compiler/OverrideServiceCompilerPass.php
namespace App\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSecurityContextCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('security.context');
        $definition->setClass('App\UserBundle\Security\Context');
    }
}