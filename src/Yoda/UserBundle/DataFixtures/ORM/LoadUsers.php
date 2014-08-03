<?php
namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Yoda\UserBundle\Entity\User;


class  LoadUsers implements FixtureInterface, ContainerAwareInterface {
	/** @var  ContainerInterface */
	private $container;

	public function load(ObjectManager $manager) {
		$names = array(
            'admin'     => 'ivanov.bg@gmail.com',
			'vlado'     => 'vlado@netreact.com',
			'silvia'    => 'daneva78@abv.bg',
			'stz184'    => 'admin@topvicove.net',
			'fazomera'  => 'fazomera2004@yahoos.com'
		);

		foreach ($names as $name => $email) {
			$user = new User();
			$user->setUsername($name);
			$user->setEmail($email);
			$user->setPassword($this->encodePassword($user, '123456'));
            $user->setRoles(array('admin' == $name ? 'ROLE_ADMIN' : 'ROLE_USER'));

			$manager->persist($user);
			$manager->flush();
		}
	}

	private function encodePassword(User $user, $plainPassword)
	{
		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		return $encoder->encodePassword($plainPassword, $user->getSalt());
	}

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}