<?php

namespace TaskManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Introduce task name')))
            ->add('description', TextareaType::class, array('attr' => array(
                'placeholder' => 'Introduce a more detailed description (optional)'
            ), 'required' => false))
            ->add('dueDate')
            ->add('category', ChoiceType::class, array('choices' => array(
                '(Uncatalogued)' => null,
                'Family' => 'Family',
                'Social' => 'Social',
                'Work' => 'Work')))
            ->add('priority', ChoiceType::class, array('choices' => array(
                'Low' => 'Low',
                'Normal' => 'Normal',
                'High' => 'High',
                'Urgent' => 'Urgent')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TaskManagerBundle\Entity\Task'
        ));
    }
}
