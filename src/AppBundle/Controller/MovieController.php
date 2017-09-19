<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Movie;
use AppBundle\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Movie controller.
 *
 * @Route("movie")
 */
class MovieController extends Controller
{
    /**
     * Lists all movie entities.
     * @Route("/{page}", name="movie_index", defaults={"page" = 1}, requirements={"page": "\d+"})
     * @Method("GET")
     */
    public function indexAction(Request $request, $page)
    {
        if ($page < 1) {
            $page = 1;
        }

        $requestVal = $request->query->all();
        $em = $this->getDoctrine()->getManager();
        $limit = $this->getParameter('app.max_movies_per_page');

        $movies = $em->getRepository(Movie::class)
            ->getResultFilterPaginated(current($requestVal), $limit, ($page - 1) * $limit);

        $nbFilteredMovies = $em->getRepository('AppBundle:Movie')->getResultFilterCount(current($requestVal));
        $pagination = $this->getPagination($requestVal, $page, 'movie_index', $limit, $nbFilteredMovies);

        return $this->render('movie/index.html.twig', array(
            'movies' => $movies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new movie entity.
     *
     * @Route("/new", name="movie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $movie = new Movie();
        $form = $this->createForm('AppBundle\Form\Type\MovieType', $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();

            $this->get(MailerService::class)->sendMail(
                $this->getParameter('email_from'),
                $this->getParameter('email_admin'),
                'film créé',
                $this->render('mail/contact_mail.html.twig', ['movie' => $movie, 'action' => 'ajout'])
            );

            return $this->redirectToRoute('movie_show', array('id' => $movie->getId()));
        }

        return $this->render('movie/new.html.twig', array(
            'movie' => $movie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a movie entity.
     *
     * @Route("/{id}", name="movie_show", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction(Movie $movie)
    {
        $deleteForm = $this->createDeleteForm($movie);

        return $this->render('movie/show.html.twig', array(
            'movie' => $movie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing movie entity.
     *
     * @Route("/{id}/edit", name="movie_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Movie $movie)
    {
        $deleteForm = $this->createDeleteForm($movie);
        $editForm = $this->createForm('AppBundle\Form\Type\MovieType', $movie, ['image' => $movie->getImage()]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movie_edit', array('id' => $movie->getId()));
        }

        return $this->render('movie/edit.html.twig', array(
            'movie' => $movie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a movie entity.
     *
     * @Route("/{id}", name="movie_delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Movie $movie)
    {
        $form = $this->createDeleteForm($movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();

            $this->get(MailerService::class)->sendMail(
                $this->getParameter('email_from'),
                $this->getParameter('email_admin'),
                'film supprimé',
                $this->render('mail/contact_mail.html.twig', ['movie' => $movie, 'action' => 'suppression'])
            );
        }

        return $this->redirectToRoute('movie_index');
    }

    /**
     * Creates a form to delete a movie entity.
     *
     * @param Movie $movie The movie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Movie $movie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movie_delete', array('id' => $movie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function getPagination($request, $page, $route, $maxPerPage, $count = null)
    {
        $em = $this->getDoctrine()->getManager();
        $pageCount = null === $count ? ceil($em->getRepository(Movie::class)->count() / $maxPerPage) : ceil($count / $maxPerPage);

        return array(
            'page' => $page,
            'route' => $route,
            'pages_count' => $pageCount,
            'route_params' => $request,
        );
    }

    /**
     * @Route("/movie-by-category", name="movies_by_category")
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function listByCategoryAction(Request $request)
    {
        $page = $request->query->get('page') ;

        if ($page < 1) {
            $page = 1;
        }

        /** @var Category $category */
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->findOneBySlug($request->query->get('category'));

        if (null === $category) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $categoryVal = $request->query->all();
        $limit = $this->getParameter('app.max_movies_per_page');
        $movies = $em->getRepository(Movie::class)

            ->getResultFilterPaginated($categoryVal, $limit, ($page - 1) * $limit);

        $nbFilteredMovies = $em->getRepository('AppBundle:Movie')->getResultFilterCount($categoryVal);
        $pagination = $this->getPagination($categoryVal, $page, 'movies_by_category', $limit, $nbFilteredMovies);

        return $this->render(
            'movie/listByCategory.html.twig',
            [
                'movies' => $movies,
                'category' => $category,
                'pagination' => $pagination
            ]
        );
    }
}
