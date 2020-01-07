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
    
    public function test_create_post():void
    {
        $this->client->request('POST', '/posts',[],[],[], json_encode([
            'title' => 'Primeiro Teste Funcional',
            'description' => 'Alguma descrição'
        ]));
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_list_post_select():void
    {
        $post = new Post("Post", "Conteudo");
        $this->em->persist($post);
        $this->em->flush();

        $this->client->request('GET', '/posts/1', [], [], [], null);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());  
    }

    public function test_delete_post():void
    {
        $post = new Post('teste', 'Apenas um teste');
        $this->em->persist($post);
        $this->em->flush();
        $this->client->request('DELETE', '/posts/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function test_create_post_with_invalid_title(): void
    {
        $this->client->request('POST', '/posts',[],[],[], json_encode([
            'title' => null,
            'description' => 'Alguma descrição'
        ]));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_update_post(): void {
        $post = new Post("test", "test");
        $this->em->persist($post);
        $this->em->flush();
        
        $this->client->request('PUT','/posts/1', [], [], [], json_encode([
            'title' => 'alteração do titulo',
            'description' => 'alteração da descricao'
            ]));    
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
    
    public function test_list_all_post(): void
    {
        $post = new Post("test", "test");
        $this->em->persist($post);
        $this->em->flush();
    
        $this->client->request('GET', '/posts', [], [], [], null);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
    
    public function test_list_post(): void
    {
        $post = new Post("test", "test");
        $this->em->persist($post);
        $this->em->flush();
    
        $this->client->request('GET', '/posts/1', [], [], [], null);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

}
?>