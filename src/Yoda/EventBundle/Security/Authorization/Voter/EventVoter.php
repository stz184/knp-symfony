<?php
/**
 * Created by PhpStorm.
 * User: stz184
 * Date: 8/9/14
 * Time: 2:57 PM
 */

namespace Yoda\EventBundle\Security\Authorization\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Yoda\EventBundle\Entity\Event;

class EventVoter implements VoterInterface {

	const EDIT = 'edit';

	/**
	 * Checks if the voter supports the given attribute.
	 *
	 * @param string $attribute An attribute
	 *
	 * @return bool    true if this Voter supports the attribute, false otherwise
	 */
	public function supportsAttribute($attribute)
	{
		return in_array(strtolower($attribute), array(
			self::EDIT
		));
	}

	/**
	 * Checks if the voter supports the given class.
	 *
	 * @param string $class A class name
	 *
	 * @return bool    true if this Voter can process the class
	 */
	public function supportsClass($class)
	{
		$supportedClass = 'Yoda\EventBundle\Entity\Event';
		return $class instanceof $supportedClass;
	}

	/**
	 * Returns the vote for the given parameters.
	 *
	 * @param TokenInterface $token A TokenInterface instance
	 * @param Event $event The object to secure
	 * @param array $attributes An array of attributes associated with the method being invoked
	 *
	 * @throws InvalidArgumentException
	 * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
	 */
	public function vote(TokenInterface $token, $event, array $attributes)
	{

		if (!$this->supportsClass($event)) {
			return VoterInterface::ACCESS_ABSTAIN;
		}

		// check if the voter is used correct, only allow one attribute
		// this isn't a requirement, it's just one easy way for you to
		// design your voter
		if(1 !== count($attributes)) {
			throw new InvalidArgumentException(
				'Only one attribute is allowed for VIEW or EDIT'
			);
		}

		$attribute = strtolower($attributes[0]);

		$user = $token->getUser();

		if (!$this->supportsAttribute($attribute)) {
			return VoterInterface::ACCESS_ABSTAIN;
		}

		/**
		 * Ensure there is a user object, i.e. that the user is logged in
		 */
		if (!$user instanceof UserInterface) {
			return VoterInterface::ACCESS_DENIED;
		}

		switch($attribute) {
			case self::EDIT:

				if ($user->getId() == $event->getOwner()->getId()) {

					return VoterInterface::ACCESS_GRANTED;
				} else {
					return VoterInterface::ACCESS_DENIED;
				}
				break;
		}
	}
}