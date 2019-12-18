<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebtestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PostControllerTest extends WebtestCase
{
    public function test_create_post():void
    {
        $client = static::createClient();
        $client->request('POST', '/posts',[],[],[], json_encode([
            'title' => 'Primeiro Teste Funcional',
            'description' => 'Alguma descrição'
        ]));
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function test_delete_post():void
    {
        $client = static::createClient();
        $client->request('DELETE', '/posts/2');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}
?>
