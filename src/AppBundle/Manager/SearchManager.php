<?php

namespace AppBundle\Manager;

class SearchManager implements searchInterface
{

    public function split($keyWords)
    {
        // TODO faire une methode pour filtrer le bruit.
        $keyWordsArray = explode(" ", $keyWords);

        return $keyWordsArray;
    }
}
