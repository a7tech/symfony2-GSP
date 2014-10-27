GSP Edition
===========

Install instructions
1. Git clone
2. composer update
3. Install db by running php app/console doctrine:schema:create
4. Load initial data by php app/console doctrine:fixtures:load
5. Admin access admin@example.com:admin

See the deploy.sh script for more info regarding steps to take

In case you want to use docker, read on:
Docker manual
-------------

Docker is container that allows you to build an exact copy of your production server without messing with other projects on your workstation

Its based on the Dockerfile, which describes all the steps needed to build the container.
See docker.io for how to install docker itself on your workstation

There are two steps, one is building the container (needed everytime you update the Dockerfile)
Building docker: 
sudo docker build -t gsp/nginx .

gsp/nginx is the name of the build, can be anything. You need it in the second step.

Want to configure mysql password, look at the ./configure-mysql.sh file in the root of the project

The second step starts docker and maps the ports
Starting docker :

sudo docker run -d -p 8005:8005 -p 9002:9002 -p 2223:22 -v ~/NetBeansProjects/Freelance/gsp/mydir/project:/server -i -t gsp/nginx
6fc633b4cdcd <- docker id that is returned. 

Want to kill a docker container (if you need to build a new one for example)
sudo kill $dockerid$

The -v option attaches the local directory to the server dir, so it can be easily updated with an editor

An ssh server runs on the container, so you can access the logs or change it's configuration:
ssh root@0.0.0.0 -p 2223 
password = root
mysqlpassword = root as well

Now visit http://0.0.0.0:8005/ and you should see the symfony2 app

See the docker/setup.sh file for what is installed in this docker container
- php 5.4
- mysql 5.5
- elasticsearch 0.90.5

How do the roles work
------------
There is a roleinfo annotation that can be added to every place where a @secure annotation is used. 
Describe the role, give a description, a module and the name of the parent role. 
See the calendarbundle for an example.

Add this to the top of the file
use App\UserBundle\Annotation\RoleInfo;

And then add this to the classblock or method block you want to describe
* @RoleInfo(role="ROLE_BACKEND_CALENDAR_ALL", parent="ROLE_BACKEND_ALL_SETTINGS", desc="All calendar access", module="Calendar")

Running the following command puts all the roles in the database
php app/console user:roles:import

Roles that are double described are only inserted once