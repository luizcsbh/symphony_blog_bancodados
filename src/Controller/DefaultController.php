<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Mailer\SimpleMailer;

class DefaultController extends AbstractController {
    private $mailer;

    public function index(string $name): Response
    {
        return $this->render('index.html.twig', ['name' => $name, ]);
    }
    public function __construct(SimpleMailer $mailer){
        $this->mailer = $mailer;
    }

    public function mail(): Response {
        $this->mailer->send('luizcsbh@gmail.com', 'Enviando mensagem');
        return new Response('<body>ok</body>');
    }

}