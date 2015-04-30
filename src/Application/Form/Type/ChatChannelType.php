<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ChatChannelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');

        $builder->add('description', 'textarea');

        $builder->add('private', 'checkbox', array(
            'required' => false,
            'label' => 'Is private?',
        ));

        $builder->add('users', 'entity', array(
            'required' => false,
            'multiple' => true,
            'expanded' => false,
            'class' => 'Application\Entity\UserEntity',
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('u')
                    ->where('u.id <> :id')
                    ->setParameter(
                        'id',
                        $options['user']->getId()
                    )
                ;
            },
            'attr' => array(
                'help_text' => 'Hold Ctrl (on Windows or Linux) or Cmd on OSX to select multiple users.',
            ),
        ));

        $builder->add('Save', 'submit', array(
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\ChatChannelEntity',
            'validation_groups' => array('newAndEdit'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'user' => false,
        ));
    }

    public function getName()
    {
        return 'chatChannel';
    }
}
