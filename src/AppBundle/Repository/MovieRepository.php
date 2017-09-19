<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class MovieRepository extends AbstractRepository implements MovieRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getResultFilterCount($requestVal)
    {
        $qb = $this->getQueryResultFilter($requestVal);
        $qb->select('COUNT(f.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function getResultFilterPaginated($requestVal, $limit = 20, $offset = 0)
    {
        $qb = $this->getQueryResultFilter($requestVal);
        $qb->orderBy('f.title', 'ASC');

        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $requestVal
     * @return QueryBuilder
     */
    public function getQueryResultFilter($requestVal)
    {
        $qb = $this->getBuilder('f');

        if (!empty($requestVal)) {

            if (!empty($requestVal['category'])) {
                $qb->leftJoin('f.category', 'c')
                    ->where('c.slug = :cat')
                   ->setParameter('cat', $requestVal['category']);
            }
        }

        return $qb;
    }

    /**
     * Initialize Query builder or reuse the current.
     *
     * @param null $qb
     * @return null
     */
    public function getQueryBuilder(QueryBuilder $qb = null)
    {
        if (null == $qb) {
            $qb = $this->createQueryBuilder('m');
        }

        return $qb;
    }

    /**
     * Return movies by one field.
     *
     * @param $keyWords
     * @return array
     */
    public function getMoviesFromKeyWords($fieldType, $keyWords, QueryBuilder $qb, $alias = 'm')
    {
        foreach ($keyWords as $key => $keyWord) {
            dump($alias.'.'.$fieldType.' LIKE :k'.$key);
            $qb
                ->orWhere($alias.'.'.$fieldType.' LIKE :k'.$key);
            $parameters['k'.$key] = '%'.$keyWord.'%';
        }
        $qb->setParameters($parameters);

        return $qb;
    }

    /**
     * Return movies by category
     *
     * Return movie
     * @param $keyWords
     */
    public function getMoviesByCategoriesAccordingKeyWords($keyWords, QueryBuilder $qb = null)
    {
        $qb = $this->getQueryBuilder($qb);
        $qb = $this->getMoviesFromKeyWords('title', $keyWords, $qb, 'c');
        $qb->join('m.category', 'c');

        return $qb;
    }

    /**
     * Return movies by movie title.
     *
     * @param $keyWords
     * @return array
     */
    public function getMoviesByTitle($keyWords, QueryBuilder $qb = null)
    {
        $qb = $this->getQueryBuilder($qb);
        $qb = $this->getMoviesFromKeyWords('title', $keyWords, $qb);

        return $qb;
    }

    /**
     * Return movies by movie title.
     *
     * @param $keyWords
     * @return array
     */
    public function getMoviesByDescription($keyWords, QueryBuilder $qb = null)
    {
        $qb = $this->getQueryBuilder($qb);
        $qb = $this->getMoviesFromKeyWords('description', $keyWords, $qb);

        return $qb;
    }

    /**
     * Get movies sorted by keywords.
     *
     * @param QueryBuilder $qb
     * @param $keyWords
     * @return mixed
     */
    public function getMoviesFiltered($keyWords)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getMoviesByCategoriesAccordingKeyWords($keyWords);
        $qb = $this->getMoviesByTitle($keyWords, $qb);
        $qb = $this->getMoviesByDescription($keyWords, $qb);


        return $qb->getQuery()->getResult();
    }
}
