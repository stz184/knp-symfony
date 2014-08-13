<?php
/**
 * Created by PhpStorm.
 * User: stz184
 * Date: 8/9/14
 * Time: 4:03 PM
 */

namespace Yoda\EventBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Yoda\EventBundle\Entity\Event as EventEntity;

class Controller extends BaseController {

	/**
	 * @return \Symfony\Component\Security\Core\SecurityContext
	 */
	public function getSecurityContext()
	{
		return $this->container->get('security.context');
	}

	/**
	 * Check is the currently logged in user has access rights to perform
	 * action on specific Event object
	 *
	 * @param \Yoda\EventBundle\Entity\Event $event
	 * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
	 */
	protected function enforceOwnerSecurity(EventEntity $event)
	{
		// keep in mind, this will call all registered security voters
		if (false === $this->get('security.context')->isGranted('edit', $event)) {
			throw $this->createAccessDeniedException('Unauthorised access by EventVoter!');
		}

	}
} 