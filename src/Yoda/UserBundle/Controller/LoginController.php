<?php
/**
 * Created by PhpStorm.
 * User: stz184
 * Date: 6/15/14
 * Time: 3:14 PM
 */

namespace Yoda\UserBundle\Controller;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller {

	/**
	 * @Route("/login", name="login"	)
	 */
	public function loginAction(Request $request)
	{
		$session = $request->getSession();

		if ($session->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $session->get(
				SecurityContext::AUTHENTICATION_ERROR
			);
		} else {
			$error = '';
		}

		return $this->render(
			'UserBundle:Login:login.html.twig',
			array(
				'last_username'	=> $session->get(SecurityContext::LAST_USERNAME),
				'error'			=> $error
			)
		);
	}
} 