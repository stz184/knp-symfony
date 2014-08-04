<?php
namespace Yoda\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\EventBundle\Entity\Event;


class  LoadEvents extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {

	public function load(ObjectManager $manager) {
		$names = array(
			'Party',
			'Birthday',
			'New Year',
			'Easter',
			'Summer Holiday',
			'Christmas',
			'Thanks Giving Day'
		);

		$locations = array(
			'ж.к. Лагера, блок 56',
			'Радомир, ж.к. Мечта',
			'София, ул. Сердика 20',
			'София, бул. 6-ти септември 12',
			'Стара Загора, ул. Христина Морфова 2',
			'Стара Загора, ул. Христо Ботев 92'
		);

		$details = array(
			'Бъдете тихи!',
			'Не казвайте на никой, изненада е!',
			'Носете си новите дрехи',
			'Спазвайте дрескода! Задължителен смокинг.',
			'По чорапи',
			'Забранено е внасянето на собствен алкохол',
			'и да не забравите: цветя не пием!'
		);


		for ($i = 1; $i <= 10; $i++) {
			shuffle($names);
			shuffle($locations);
			shuffle($details);

			$event = new Event();
			$event->setName($names[0]);
			$event->setLocation($locations[0]);
			$event->setTime(new \DateTime('+ '.rand(1, 90).' days'));
			$event->setDetails($details[0]);
			$event->setOwner($this->getReference('user-'.rand(0,4)));
			$manager->persist($event);
			$manager->flush();
		}
	}

	/**
	 * Get the order of this fixture
	 *
	 * @return integer
	 */
	function getOrder()
	{
		return 20;
	}
}