<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebtestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Valitador\Valitador\ValitadorInterface;

class PostControllerTest extends WebtestCase
{
    private EntityManagerInterface $em;

    private KernelBrowser $client;
    
    public function test_create_post():void
    {
        $this->client->request('POST', '/posts',[],[],[], json_encode([
            //'title' => 'Primeiro Teste Funcional',
            'description' => 'Alguma descrição'
        ]));
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_delete_post():void
    {
        $post = new Post('teste', 'Apenas um teste');
        $this->em->persist($post);
        $this->em->flush();
        $this->client->request('DELETE', '/posts/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function setUp(): void
    {
        $this->client = self::createClient();
        
        //cria a ferramenta para manipulação do banco de dados
        $this->em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $tool = new SchemaTool($this->em);

        //Recupera meta informaçãoda entidade Post
        $metadata = $this->em->getClassMetadata(Post::class);

        //apaga a tabelaassociada à entidade Post
        $tool->dropSchema([$metadata]);

        try{
            //cria a tabela associada à entidade Post
            $tool->createSchema([$metadata]);
        } catch (ToolsException $e){
            $this->fail("Impossível criar tabela Post:". $e->getMessage());
        }
    }

    public function test_create_post_with_invalid_title(): void
    {
        $this->client->request('POST', '/posts',[],[],[], json_encode([
            'title' => 1234,
            'description' => 'Alguma descrição'
        ]));
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

}
?>
 