<?php
/**
 * Created by PhpStorm.
 * User: stz184
 * Date: 7/27/14
 * Time: 2:56 PM
 */

namespace Yoda\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterFormType extends AbstractType {

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName()
	{
		return 'user_register';
	}


	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', 'text', array(

		))
		->add('email', 'email')
		->add('plainPassword', 'repeated', array(
			'type' 				=> 'password',
			'invalid_message' 	=> 'The password fields must match.',
			'first_options'		=> array(
				'label'	=> 'Password'
			),
			'second_options'	=> array(
				'label'	=> 'Repeat Password'
			),
		))
		->add('save', 'submit', array(
			'attr' => array(
				'class' => 'btn btn-primary pull-right'
			),
			'label' => 'Register!'
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Yoda\UserBundle\Entity\User'
		));
	}
}