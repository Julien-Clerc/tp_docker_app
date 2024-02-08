<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/blogs', name: 'app_blog', methods: ['GET'])]
    public function getAll(): Response
    {
        $blogs = $this->entityManager->getRepository(Blog::class)->findAll();

        // $content = [];

        // foreach($blogs as $blog) {
        //     $item = [
        //         'id' => $blog->getId(),
        //         'title' => $blog->getTitle(),
        //         'content' => $blog->getContent()
        //     ];
        //     $content[] = $item;
        // }

        return $this->render('blogs.html.twig', [
            'blogs' => $blogs,
        ]);    }

    #[Route('/blogs', name: 'create_blog', methods: ['POST'])]
    public function createBlog(Request $request): Response
    {
        // Récupérer les données du corps de la requête
        $requestData = json_decode($request->getContent(), true);

        // Vérifiez si les données attendues sont présentes
        if (!isset($requestData['title']) || !isset($requestData['content'])) {
            return $this->json(['error' => 'Title and content are required'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouvel objet Blog avec les données de la requête
        $blog = new Blog();
        $blog->setTitle($requestData['title']);
        $blog->setContent($requestData['content']);

        // Enregistrer le nouvel objet dans la base de données
        $this->entityManager->persist($blog);
        $this->entityManager->flush();

        // Récupérer tous les blogs (y compris le nouveau) et les renvoyer en réponse
        return $this->getAll();
    }
}
