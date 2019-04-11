<?php // src/Controller/DefaultController.php

namespace App\Controller;
use App\Entity\Articles;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;

class DefaultController extends AbstractController
{
  public function index(Request $request): Response
  {
    // Remove all the products from the cart
    $em = $this->getDoctrine()->getEntityManager();
    $articles = $em->getRepository(Articles::class)->findAll();
    foreach ($articles as $article) {
        $article->setAddInCart(0);
        $em->persist($article);
        $em->flush();
    }

    return $this->render('home.html.twig');
  }

  public function countArticles(Request $request): Response
  {
    $dm = $this->getDoctrine()->getManager();
    $qb = $dm->createQueryBuilder();

    $qb->select($qb->expr()->count('pro'))
       ->from(Articles::class, 'pro')
       ->where('pro.addInCart = :add')
       ->setParameter('add', true);

    $query = $qb->getQuery();
    $count = $query->getSingleScalarResult();
    // var_dump($count);
    // die();
    return new Response($count);
  }
}
