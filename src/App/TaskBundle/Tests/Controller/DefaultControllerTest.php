<?php

namespace App\TaskBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/World');

        $this->assertTrue($crawler->filter('html:contains("Hello World")')->count() > 0);
    }
}
