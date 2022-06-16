<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('nomCat')
            ->add('User')*/
            ->add('nomCat', TextType::class, array('label' => 'Nom de la categorie', 'attr' => array('require' => 'require', 'class' => 'form-control form-group')))
            //->add('User', TextType::class, array('label'=>'Utilisateur', 'attr'=>array('require'=>'require','class'=>'form-control form-group')))
            ->add('Valider', SubmitType::class, array('attr' => array('class' => 'btn btn-success form-group',)));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}