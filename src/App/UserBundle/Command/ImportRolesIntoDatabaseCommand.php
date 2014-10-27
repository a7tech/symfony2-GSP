<?php
// src/Acme/DemoBundle/Command/GreetCommand.php
namespace App\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;
use App\UserBundle\Entity\Role;

class ImportRolesIntoDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:roles:import')
            ->setDescription('Import all secure annotations into database.')
            ->setHelp(<<<EOT
                Parses all controller secure annotations and pulls the role information into the database
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $annotationReader = $this->getContainer()->get('annotation_reader');

        $annotationClass = 'App\UserBundle\Annotation\RoleInfo';

        $roles = array();

        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        // Find all Controller files we want to read the annotations from
        $finder = new Finder();
        $finder->files()->in($rootDir . '/../src/App/*/Controller/*')->in($rootDir . '/../src/App/*/Controller');

        foreach ($finder as $file) {
            $fp = fopen($file, 'r');
            $class = $namespace = $buffer = '';
            $i = 0;
            while (!$class) {
                if (feof($fp)) break;

                $buffer .= fread($fp, 512);
                $tokens = @token_get_all($buffer);  // Ignore unterminated comment stringsmysq

                if (strpos($buffer, '{') === false) continue;

                for (;$i<count($tokens);$i++) {
                    if ($tokens[$i][0] === T_NAMESPACE) {
                        for ($j=$i+1;$j<count($tokens); $j++) {
                            if ($tokens[$j][0] === T_STRING) {
                                 $namespace .= '\\'.$tokens[$j][1];
                            } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                                 break;
                            }
                        }
                    }

                    if ($tokens[$i][0] === T_CLASS) {
                        for ($j=$i+1;$j<count($tokens);$j++) {
                            if ($tokens[$j] === '{') {
                                $class = $tokens[$i+2][1];
                            }
                        }
                    }
                }
            }
            $classname = $namespace . "\\" . $class;
            $classes[] = substr($classname,1);
        }

        // Loop through all classes and read their annotations
        foreach($classes as $classname){
            $reflectionClass = new \ReflectionClass($classname);

            $annotation = $annotationReader->getClassAnnotation($reflectionClass, $annotationClass);
            if(!is_null($annotation)){
                //var_dump($annotation);
                $foundroles[] = $annotation;
            }

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $annotation = $annotationReader->getMethodAnnotation($reflectionMethod, $annotationClass);
                if(!is_null($annotation)){
                  //  var_dump($annotation);
                    $foundroles[] = $annotation;
                }            
            }
        }
        // Now check all roles and deduplicate
        foreach ($foundroles as $ann) {
            // Check if the role already exists in the database, if so use that so we can append
            $dbrole = $manager->getRepository('AppUserBundle:Role')->findOneBy(array('role' => $ann->role));
            if (!is_null($dbrole)) {
                $role = $dbrole;
            } else {
                // Not found, create a new role object
                $role = new Role($ann->role);
            }
            $role->setDescription($ann->desc);
            $role->setModule($ann->module);
            $roles[$ann->role] = array('role' => $role, 'parent' => $ann->parent);
        }

        // Now create the role objects and the hyrarchy
        foreach($roles as $name => $role){
            $obj = $role['role'];
            $parentname = $role['parent'];
            if (isset($roles[$parentname])) {
                $objparent = $roles[$parentname]['role'];
                $obj->setParent($objparent);
            } elseif($parentname == 'null') {
                // Highest level, no parent
            } else {
                $output->writeln('Missing parent role:' . $parentname);
                $dbrole = $manager->getRepository('AppUserBundle:Role')->findOneBy(array('role' => $parentname));
                if (!is_null($dbrole)) {
                    $obj->setParent($dbrole);
                    $output->writeln('Found in database ' . $parentname);
                } else {
                    $output->writeln('This role is not defined anywhere, check!' . $parentname);
                }
            }            
            $validator = $this->getContainer()->get('validator');
            $errors = $validator->validate($obj);

            if (count($errors) > 0) {
                // if we have errors, this one already exists in the database
                print (string) $obj .  $errors;
                continue;
            }

            $output->writeln('Persisting : ' . $obj->getRole() . ' , Parent: ' . $parentname);
            $manager->persist($obj);
        }

        try {
           $manager->flush();
        } catch (\Doctrine\DBAL\DBALException $e) {
            // ... Error on database call
            print $e;
        }

        $output->writeln('Everything done');
    }
}