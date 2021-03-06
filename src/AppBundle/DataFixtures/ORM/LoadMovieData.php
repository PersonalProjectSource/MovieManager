<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\HashTag;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Movie;

class LoadMovieData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category1 = $manager->getRepository("AppBundle:Category")->findOneByTitle('Comédie');
        $category3 = $manager->getRepository("AppBundle:Category")->findOneByTitle('Policier');
        $category4 = $manager->getRepository("AppBundle:Category")->findOneByTitle('Drame');

        $movie1 = new Movie();
        $movie1->setTitle('Léon');
        $movie1->setDescription('Léon (Jean Reno) est un tueur à gages vivant seul au quartier de la Little Italy à New York. La plupart de ses contrats viennent d\'un mafieux nommé Tony (Danny Aiello) qui opère depuis son restaurant le « Supreme Macaroni ». Léon passe son temps libre à faire des exercices physiques, prendre soin de sa plante d\'intérieur (une Aglaonema) qu\'il décrit comme sa « meilleure amie » et regarder des comédies musicales de Gene Kelly.');
        $movie1->setCategory($category3);
        $manager->persist($movie1);

        $movie2 = new Movie();
        $movie2->setTitle('Brice de Nice');
        $movie2->setDescription('Brice Agostini mène la belle vie à Nice. Il est fan du film Point Break et en particulier de son personnage principal Bodhi joué par Patrick Swayze. Il attend chaque jour qu\'une vague géante déferle sur les rives de Nice, comme cela a eu lieu une fois, en 1979.');
        $movie2->setCategory($category1);
        $manager->persist($movie2);

        $movie3 = new Movie();
        $movie3->setTitle('Le Dernier Métro');
        $movie3->setDescription('Depuis que la moitié Nord de la France a été envahie par les nazis, les parisiens passent leurs soirées dans les salles de spectacles, pour ne pas avoir froid. Marion Steiner ne pense qu\'aux répétitions de la pièce qui va être jouée dans son théâtre, le théâtre Montmartre, dont elle assure la direction à la place de son mari juif. Tout le monde pense que Lucas Steiner a fui la France. En réalité, il s\'est réfugié dans les sous-sols du théâtre. Chaque soir, Marion lui rend visite et commente avec lui le travail des comédiens, notamment celui du jeune premier de la troupe, Bernard Granger. Très vite, Lucas comprend, simplement en écoutant les répétitions depuis sa cachette, que sa femme est tombée amoureuse de Bernard Granger. Ce dernier, engagé dans la résistance, sera le seul de la troupe à aider Lucas lors d\'une perquisition de la gestapo. La pièce est un succès mais le théâtre connait de durs jours, du fait de la jalousie d\'un critique de théâtre antisémite et hargneux. Alors que la France est libérée par les alliés, Marion continue sa vie de comédienne, entre son mari, désormais réhabilité et acclamé, et Bernard.');
        $movie3->setCategory($category4);
        $manager->persist($movie3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
