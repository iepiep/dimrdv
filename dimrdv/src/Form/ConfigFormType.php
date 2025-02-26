<?php

namespace Iepiep\Dimrdv\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DIMRDV_GOOGLE_API_KEY', TextType::class, [
                'label' => 'Clé API Google Maps', // Use translation key
                'required' => true,
            ]);
    }
}
