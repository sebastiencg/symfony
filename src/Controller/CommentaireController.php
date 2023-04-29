<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }
    #[Route('/commentaire/create/{id}',name: 'create_comment')]
    public function create(Request $request , EntityManagerInterface $manager, Post $post): Response
    {
        $commentaire= new commentaire();
        var_dump($commentaire);
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);
        $formCommentaire->handleRequest($request);
        if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){
            $commentaire->setCreatedAt(new \DateTime());
            /* $commentaire->setAuthor($this->getUser());*/
            $commentaire->setPost($post);
            $manager->persist($commentaire);
            $manager->flush();
        }
        return $this->redirectToRoute('show_post',['id'=>$commentaire->getPost()->getId()]);

    }
}
