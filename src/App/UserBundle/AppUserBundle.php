<?php

namespace App\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\UserBundle\DependencyInjection\Compiler\OverrideSecurityContextCompilerPass;

class AppUserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideSecurityContextCompilerPass());
    }
}
