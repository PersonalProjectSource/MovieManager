<?php


namespace AppBundle\Handler;

use AppBundle\Repository\MovieRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MovieHandler
{
    private $repository;

    public function __construct(MovieRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMoviesBySearch(Request $request)
    {
        $movies = [];
        $keyWords = explode(" ", $request->request->get('search'));
        $movies = $this->repository->getMoviesFiltered($keyWords);

        return $movies;
    }
}