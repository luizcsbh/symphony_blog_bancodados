<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PostController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/post", methods={"POST"})
     */
    public function create():Response
    {
        $post = new Post("Minha primeira aplicação com Syfony","Descrição da minha aplicação");
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new Response('Ok', Response::HTTP_CREATED);
    }
}
?>