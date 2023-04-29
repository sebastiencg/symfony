<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Form\CommentaireType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index_posts')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts'=>$postRepository->findBy(
                [],
                ['id' => 'DESC']
            )
        ]);
    }

    #[Route('/show/{id}', name: 'show_post')]
    public function show(Post $post):Response
    {

        $comment = new Commentaire();
        $commentForm = $this->createForm(CommentaireType::class, $comment);

        return $this->renderForm('post/show.html.twig', [
            'post'=>$post,
            'commentForm'=>$commentForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_post')]
    public function delete(Post $post , EntityManagerInterface $manager) : Response
    {

        if ($post){
            $manager->remove($post);
            $manager->flush();
        }
        return $this->redirectToRoute('index_posts');
    }

    #[Route('/create/', name: 'create_post', priority: 2)]
    #[Route('/edit/{id}', name: 'update_post', priority: 2)]


    public function create(EntityManagerInterface $manager, Request $request,Post $post=null )
    {
        $edit = false;
        if($post){
            $edit = true;
        }
        if(!$edit){
            $post = new Post();

        }
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $post->setAuthor($this->getUser());
            if(!$edit){
                $post->setCreatedAt(new \DateTime());
            }
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('index_posts');
        }
        return $this->renderForm('post/create.html.twig', [
            'form'=>$form,
            'edit'=>$edit
        ]);
    }

}
