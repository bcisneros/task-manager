<?php

namespace TaskManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('name', TextType::class, array(
                'attr' => array('placeholder' => 'task.form.placeholder.name', 'class' => 'form-control'),
                'label' => 'task.form.label.name'
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array('placeholder' => 'task.form.placeholder.description', 'class' => 'form-control'),
                'required' => false,
                'label' => 'task.form.label.description'
            ))
            ->add('dueDate', DateTimeType::class,
                array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm',
                    'label' => 'task.form.label.due_date',
                    'attr' => array('class' => 'form-control', 'placeholder' => 'task.form.placeholder.due_date')))
            ->add('category', ChoiceType::class, array(
                'choices' => array(
                    'task.form.label.category.family' => 'Family',
                    'task.form.label.category.social' => 'Social',
                    'task.form.label.category.work' => 'Work'),
                'attr' => array('class' => 'form-control'),
                'required' => false,
                'placeholder' => 'task.form.placeholder.category',
                'empty_data' => null,
                'label' => 'task.form.label.category'
            ))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'task.form.label.priority.low' => 'Low',
                    'task.form.label.priority.normal' => 'Normal',
                    'task.form.label.priority.high' => 'High',
                    'task.form.label.priority.urgent' => 'Urgent'),
                'attr' => array('class' => 'form-control'),
                'label' => 'task.form.label.priority'
            ));
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
