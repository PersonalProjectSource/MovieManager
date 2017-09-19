<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'film.titre'))
            ->add('description', TextareaType::class, array('label' => 'film.description'))
            // if an image has previously been uploaded, we populate the movie object with database values
            ->add('image', ImageType::class, array('data' => $options['image']))

	        ->add('category', EntityType::class, array(
		        'class' => 'AppBundle\Entity\Category',
		        'multiple' => false,
		        'required' => false,
		        'label' => 'film.categorie',
		        'placeholder' => 'film.categories.toutes',
		        'query_builder' => function (EntityRepository $er) {
			        return $er->createQueryBuilder('c')
			                  ->orderBy('c.title', 'ASC');
		        },
	        ));

        $builder->add('Valider', SubmitType::class, array(
            'attr' => ['class' => 'btn btn-primary btn-lg btn-block'],
            'label' => 'valider'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Movie',
                'image' => null,
        ));
    }
}
