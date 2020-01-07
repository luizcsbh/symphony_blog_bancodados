<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostController
{
    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    private ValidatorInterface $valitador;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $valitador)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->valitador = $valitador;
    }

    /**
     * @Route("/posts", methods={"POST"})
     */
    public function create(Request $request):Response
    {
        $post = $this->serializer->deserialize($request->getContent(), Post::class, 'json');

        $erros = $this->validator->validate($post);

        if (count($erros)){
            throw new ValidationException($erros);
        }
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new Response('Ok', Response::HTTP_CREATED);
    }

    /**
     * @Route("/posts/{id}", methods={"GET"})
     */
    public function details(int $id):Response
    {
        /**@var Post $post */
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        
        if (null === $post){
            throw new NotFoundHttpException('Post não encontrado');
        }
        return JsonResponse::fromJsonString($this->serializer->serialize($post, 'json'));
    }

    /**
     * @Route("/posts", methods={"GET"})
     */
    public function index():Response
    {
        /**@var Post {} $posts */
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        
        return JsonResponse::fromJsonString($this->serializer->serialize($post, 'json'));
    }

    /**
     * @Route("/posts/{id}", methods={"PUT"})
     */
    public function update(Request $request, int $id):Response
    {
        /**@var Post $post */
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        $data = json_decode($request->getContent(), true);

        $post->title = $data['title'];
        $post->description = $data['description'];
        
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new Response('Ok');
    }

    /**
     * @Route("/posts/{id}", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        /**@var Post $post */
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return new Response('',Response::HTTP_NO_CONTENT);
    } 
}
?>