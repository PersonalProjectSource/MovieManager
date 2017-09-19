<?php
/**
 * Created by PhpStorm.
 * User: laurentbrau
 * Date: 12/07/2017
 * Time: 11:20
 */

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;


interface MovieRepositoryInterface
{
    public function getMoviesFromKeyWords($fieldType, $keyWords, QueryBuilder $qb, $alias);
    public function getMoviesByTitle($keyWords, QueryBuilder $qb);
    public function getMoviesByCategoriesAccordingKeyWords($keyWords, QueryBuilder $qb);
    public function getMoviesFiltered($keyWords);
}