<?php
namespace Yoda\UserBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;

class RegisterController extends Controller
{
	private $providerKey = 'secured_area';

	/**
	 * @Route("/register", name="user_register")
	 * @Template
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return Response
	 */
    public function registerAction(Request $request)
    {
		$userDefaults = new User();
		$userDefaults->setUsername('Leia');
        $form = $this->createForm(new RegisterFormType(), $userDefaults);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var User $user */
			$user = $form->getData();
			$user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

			/** @var ObjectManager $entityManager */
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			$this->authenticateUser($user);

			$sessionKey = '_security.'.$this->providerKey.'.target_path';
			$session	= $request->getSession();

			$session->getFlashBag()->add(
				'success',
				'Welcome to the Death Star! Have a magical day!'
			);

			if ($session->has($sessionKey)) {
				$url = $session->get($sessionKey);
				$session->remove($sessionKey);
			} else {
				$url = $this->generateUrl('event');
			}

			return $this->redirect($url);
		}

        return array('form' => $form->createView());
    }

	private function encodePassword(User $user, $plainPassword)
	{
		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		return $encoder->encodePassword($plainPassword, $user->getSalt());
	}

	private function authenticateUser(User $user)
	{
		$token = new UsernamePasswordToken($user, null, $this->providerKey, $user->getRoles());
		$this->getSecurityContext()->setToken($token);
	}
}