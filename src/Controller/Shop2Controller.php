<?php // src/Controller/Shop2Controller.php

namespace App\Controller;
use App\Entity\Articles;
use App\Entity\Payement;
use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Shop2Controller extends AbstractController
{
  public function index()
  {
      $test = $this->forward('App\Controller\DefaultController::countArticles');
      json_encode($test);
      $countArticlesCart = substr($test, -1);

      $em = $this->getDoctrine()->getManager();
      $articles = $em
          ->getRepository(Articles::class)
          ->findAll();

      return $this->render('shop2/home.html.twig', array(
          'articles' => $articles,
          'countArticlesCart' => $countArticlesCart
      ));
  }

  public function indexCategories($slug)
  {
      $em = $this->getDoctrine()->getManager();
      $em2 = $this->getDoctrine()->getManager();

      // Id with slug
      $idCategory = $em
          ->getRepository(Category::class)
          ->findOneBySlug($slug);

      // Articles
      $articles = $em2
          ->getRepository(Articles::class)
          ->createQueryBuilder('e')
          ->where('e.articleCategory = :articleCategory')
          ->setParameter('articleCategory', $idCategory)
          ->getQuery()
          ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      if (!$articles) {
          throw $this->createNotFoundException('Unable to find Articles entity.');
      }

      return $this->render('shop2/home.html.twig', array(
          'articles' => $articles
      ));
  }

  // Display the cart view: CART FULL or PAYEMENT or NOTHING
  public function cart()
  {
      $var = $this->forward('App\Controller\DefaultController::countArticles');
      json_encode($var);
      $countArticlesCart = substr($var, -1);

      $articlesInCart;
      $em1 = $this->getDoctrine()->getManager();
      $em2 = $this->getDoctrine()->getManager();

      $articlesInCart = $em1
          ->getRepository(Articles::class)
          ->createQueryBuilder('e')
          ->where('e.addInCart = :addInCart')
          ->setParameter('addInCart', true)
          ->getQuery()
          ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

      $payementPaid = $em2
          ->getRepository(Payement::class)
          ->findOneBy(['payementPaid' => true]);

      if(count($articlesInCart) > 0) {
          // Total cart
          $totalCart = 0;
          for ($i=0; $i < count($articlesInCart); $i++) {
            $totalCart = $articlesInCart[$i]['price'] + $totalCart;
          }

          return $this->render('shop2/cart.html.twig', array(
              'articlesInCart' => $articlesInCart,
              'totalCart' => $totalCart,
              'countArticlesCart' => $countArticlesCart
          ));
      } else if (count($payementPaid) > 0){

          return $this->render('shop2/cart.html.twig', array(
              'payement' => $payementPaid
          ));
      } else {
          return $this->render('shop2/cart.html.twig');
      }
  }

  // Display the payement page
  public function payementInfo(Request $request)
  {
    $var = $this->forward('App\Controller\DefaultController::countArticles');
    json_encode($var);
    $countArticlesCart = substr($var, -1);

    $articlesInCart;
    $em1 = $this->getDoctrine()->getManager();
    $em2 = $this->getDoctrine()->getManager();

    $articlesInCart = $em1
        ->getRepository(Articles::class)
        ->createQueryBuilder('e')
        ->where('e.addInCart = :addInCart')
        ->setParameter('addInCart', true)
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    $em = $this->getDoctrine()->getManager();
    $articlesInCart = $em
        ->getRepository(Articles::class)
        ->createQueryBuilder('e')
        ->where('e.addInCart = :addInCart')
        ->setParameter('addInCart', true)
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    // Total cart
    $totalCart = 0;
    for ($i=0; $i < count($articlesInCart); $i++) {
      $totalCart = $articlesInCart[$i]['price'] + $totalCart;
    }
    return $this->render('shop2/payement.html.twig', array(
        'totalCart' => $totalCart,
        'articlesInCart' => $articlesInCart
    ));
  }

  // Get all payement & shipping information (AJAX)
  public function shippingInfo(Request $request)
  {
      $entityManager = $this->getDoctrine()->getManager();
      // New $payement
      $payement = new Payement();
      $name = $request->request->get('name');
      $cvv = $request->request->get('cvv');
      $cardNumber = $request->request->get('cardNumber');
      $city = $request->request->get('city');
      $cpCity = $request->request->get('cpCity');
      $totalCart = $request->request->get('cartFloat');

      // Create $payement
      $payement->setName($name);
      $payement->setCreditCardNumber($cardNumber);
      $payement->setCodeSecurity($cvv);
      $payement->setCity($city);
      $payement->setCodePostal($cpCity);
      $payement->setPayementPaid(true);
      $payement->setTotalPrice($totalCart);

      // tell Doctrine you want to (eventually) save the $payement (no queries yet)
      $entityManager->persist($payement);

      // actually executes the queries (i.e. the INSERT query)
      $entityManager->flush();

      // ///
      // Remove all the products from the cart
      $em2 = $this->getDoctrine()->getEntityManager();
      $articles = $em2->getRepository(Articles::class)->findAll();

      foreach ($articles as $article) {
          $article->setAddInCart(0);
          $em2->persist($article);
          $em2->flush();
      }

      return new Response("Payement OK");
  }
}
