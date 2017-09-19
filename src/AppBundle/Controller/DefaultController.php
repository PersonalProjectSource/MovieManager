<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Movie;
use AppBundle\Handler\MovieHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', array(
            'categories' => $this->getDoctrine()->getManager()->getRepository(Category::class)->findAll(),
        ));
    }

    /**
     * @Route("/search", name="searchCtrl")
     */
    public function searchAction(Request $request)
    {
        $movies = $this->get('app.handler.movie')->getMoviesBySearch($request);

        return $this->render('movie/index.html.twig', array(
            'movies' => $movies
        ));
    }
}
