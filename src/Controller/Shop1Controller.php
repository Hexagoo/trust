<?php // src/Controller/Shop1Controller.php

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

class Shop1Controller extends AbstractController
{
  public function index()
  {
      $em = $this->getDoctrine()->getManager();
      $articles = $em
          ->getRepository(Articles::class)
          ->findAll();

      // Cart articles
      $var = $this->forward('App\Controller\DefaultController::countArticles');
      json_encode($var);
      $countArticlesCart = substr($var, -1);

      return $this->render('shop1/home.html.twig', array(
          'articles' => $articles,
          'countArticlesCart' => $countArticlesCart
      ));
  }

  public function indexCategories($slug)
  {
    // Cart articles
    $var = $this->forward('App\Controller\DefaultController::countArticles');
    json_encode($var);
    $countArticlesCart = substr($var, -1);

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

    return $this->render('shop1/home.html.twig', array(
        'articles' => $articles,
        'countArticlesCart' => $countArticlesCart
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

          return $this->render('shop1/cart.html.twig', array(
              'articlesInCart' => $articlesInCart,
              'totalCart' => $totalCart,
              'countArticlesCart' => $countArticlesCart
          ));
      } else if (count($payementPaid) > 0){

          return $this->render('shop1/cart.html.twig', array(
              'payement' => $payementPaid
          ));
      } else {
          return $this->render('shop1/cart.html.twig');
      }
  }

  // Add product to cart (AJAX)
  public function addProductToCart(Request $request)
  {
      $em = $this->getDoctrine()->getEntityManager();
      $id = $request->request->get('id');
      $entity = $em->getRepository(Articles::class)->find($id);

      $entity->setAddInCart(1);
      $em->persist($entity);
      $em->flush();

      return new Response("OK");
  }

  // Remove a product form the cart (AJAX)
  public function removeProductFromCart(Request $request)
  {
      $em = $this->getDoctrine()->getEntityManager();
      $id = $request->request->get('id');
      $entity = $em->getRepository(Articles::class)->find($id);

      $entity->setAddInCart(0);
      $em->persist($entity);
      $em->flush();

      return new Response("OK");
  }

  // Display the payement page
  public function displayPayementForm(Request $request)
  {
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
    return $this->render('shop1/payement.html.twig', array(
        'totalCart' => $totalCart
    ));
  }

  // Get all payement information (AJAX)
  public function saveCreditCardInfo(Request $request)
  {
      $entityManager = $this->getDoctrine()->getManager();
      // New $payement
      $payement = new Payement();
      $name = $request->request->get('name');
      $cvv = $request->request->get('cvv');
      $cardNumber = $request->request->get('cardNumber');
      $totalCart = $request->request->get('cartFloat');

      // Create $payement
      $payement->setName($name);
      $payement->setCreditCardNumber($cardNumber);
      $payement->setCodeSecurity($cvv);
      $payement->setTotalPrice($totalCart);

      // tell Doctrine you want to (eventually) save the $payement (no queries yet)
      $entityManager->persist($payement);

      // actually executes the queries (i.e. the INSERT query)
      $entityManager->flush();
      $idPayement = $payement->getId();

      return new Response($idPayement);
  }

  // Display the shipping page
  public function displayShippingForm(Request $request, $idPayement)
  {
    return $this->render('shop1/payement_shipping.html.twig', array(
        'idPayement' => $idPayement
    ));
  }

  // Get all shipping information (AJAX)
  public function saveShippingInfo(Request $request)
  {
    // Get AJAX info
    $city = $request->request->get('city');
    $cpCity = $request->request->get('cpCity');
    $idPayement = $request->request->get('idPayement');

    // Set payement already done
    $em = $this->getDoctrine()->getEntityManager();
    // $entity = $em->getRepository(Payement::class)->find(array(), array(‘id’ => ‘DESC’));
    $entity = $em->getRepository(Payement::class)->find($idPayement);
    $entity->setCity($city);
    $entity->setCodePostal($cpCity);

    $em->persist($entity);
    $em->flush();

    return new Response($idPayement);
  }

  // Display the shipping page
  public function displayConfirmationView(Request $request, $idPayement)
  {
    return $this->render('shop1/payement_confirmation.html.twig', array(
        'idPayement' => $idPayement
    ));
  }

  // Get all shipping information (AJAX)
  public function confirmationPaiement(Request $request)
  {
    // Set the payementPaid at true
    $idPayement = $request->request->get('idPayement');
    // Set payement already done
    $em = $this->getDoctrine()->getEntityManager();
    // $entity = $em->getRepository(Payement::class)->find(array(), array(‘id’ => ‘DESC’));
    $entity = $em->getRepository(Payement::class)->find($idPayement);
    $entity->setPayementPaid(true);

    $em->persist($entity);
    $em->flush();

    // Remove all the products from the cart
    $em2 = $this->getDoctrine()->getEntityManager();
    $articles = $em2->getRepository(Articles::class)->findAll();
    foreach ($articles as $article) {
        $article->setAddInCart(0);
        $em2->persist($article);
        $em2->flush();
    }

    return new Response("OK");
  }
}
