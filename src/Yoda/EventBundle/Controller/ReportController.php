<?php
/**
 * Created by PhpStorm.
 * User: stz184
 * Date: 9/14/14
 * Time: 9:06 PM
 */

namespace Yoda\EventBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yoda\EventBundle\Entity\Event;


class ReportController extends Controller {
	/**
	 * @Route("/events/report/recentlyUpdated.csv")
	 */
	public function updatedEventsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$events = $em->getRepository('EventBundle:Event')->getUpcomingEvents();

		$fp = fopen('php://temp', 'w');
		fputcsv($fp, array(
			'ID',
			'Name',
			'Location',
			'Time'
		));

		/** @var Event $event */
		foreach ($events as $event) {
			fputcsv($fp, array(
				$event->getId(),
				$event->getName(),
				$event->getLocation(),
				$event->getTime()->format('d.m.Y H:i')
			));
		}

		rewind($fp);

		$response = new Response(stream_get_contents($fp));
		$response->headers->set('Content-Type', 'text/csv');

		fclose($fp);

		return $response;

	}
} 