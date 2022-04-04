<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;



class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article')]
    public function index(ManagerRegistry $doctrine, Request $request) : Response
    {
        $page = $request->get('page', 1);
        $em = $doctrine->getManager();

        $articles = $em->getRepository(Article::class)->getArticleList($page);

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
        ]);
    }

    #[Route('/article/single/{article}', name: 'single_article')]
    public function single(Article $article){

        return $this->render('article/single.html.twig', [
           'article' => $article
        ]);
    }

    #[Route('/article/create', name: 'create_article')]
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger){

        $article = new Article();
        $user = $this->getUser()->getUserIdentifier();
        $article->setAuthor($user);
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();
            $image = $form->get('image')->getData();
            if($image){
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('img_directory'),
                    $newFilename
                );

                $article->setImage($newFilename);
            }
            $article->setCreatedAt(new \DateTime('now'));

            $em = $doctrine->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{article}', name: 'article_delete')]
    public function delete(Article $article,  ManagerRegistry $doctrine){

        $em = $doctrine->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('app_article');
    }



}
