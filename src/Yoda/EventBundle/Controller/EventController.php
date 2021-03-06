<?php

namespace Yoda\EventBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Entity\EventRepository;
use Yoda\EventBundle\Form\EventType;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{

    /**
     * Lists all Event entities.
     * @Template("EventBundle:Event:index.html.twig")
	 * @Route("/", name="event")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

		/** @var EventRepository $eventRepo */
        $eventRepo 	= $em->getRepository('EventBundle:Event');
		$entities 	= $eventRepo->getUpcomingEvents();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Event entity.
	 * @Route("/create", name="event_create")
     */
    public function createAction(Request $request)
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
			$entity->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl(
				'event_show',
				array('slug' => $entity->getSlug())
			));
        }

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Event entity.
	*
    * @param Event $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
	 * @Route("/new", name="event_new")
     */
    public function newAction()
    {
        $entity = new Event();
        $form   = $this->createCreateForm($entity);

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
	 * @Route("/{slug}/show", name="event_show")
     */
    public function showAction($slug)
    {
		/** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->findOneBy(array(
			'slug' => $slug
		));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('EventBundle:Event:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
		));
    }

    /**
     * Displays a form to edit an existing Event entity.
	 * @Route("/{id}/edit", name="event_edit")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

		$this->enforceOwnerSecurity($entity);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Event entity.
    *
    * @param Event $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
			'label' => 'Update',
			'attr'	=> array(
				'class'	=> 'button'
			)
		));

        return $form;
    }
    /**
     * Edits an existing Event entity.
     * @Route("/{id}/update", name="event_update")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

		$this->enforceOwnerSecurity($entity);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Event entity.
     * @Route("/{id}/delete", name="event_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

			$this->enforceOwnerSecurity($entity);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array(
				'class' => 'button'
			)))
            ->getForm()
        ;
    }

	/**
	 * @param integer $id
	 * @param string $format
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @Route("{id}/attend.{format}", name="attend_event", requirements={"id" : "\d+", "format" : "json"}, defaults={"format" : "html"})
	 */
	public function attendAction($id, $format)
	{
		$enityManager = $this->getDoctrine()->getManager();
		/** @var Event $event */
		$event = $enityManager->getRepository('EventBundle:Event')->find($id);

		if (!$event) {
			throw $this->createNotFoundException(sprintf('No event found for ID %d', $id));
		}

		if (!$event->hasAttendee($this->getUser())) {
			$event->getAttendees()->add($this->getUser());
			$enityManager->persist($event);
			$enityManager->flush();
		}

		if ($format == 'json') {
			return new JsonResponse(array(
				'attending' => true
			));
		}

		$url = $this->generateUrl('event_show', array(
			'slug'	=> $event->getSlug()
		));

		return $this->redirect($url);
	}

	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @Route("{id}/unattend.{format}", name="unattend_event", requirements={"id" : "\d+", "format" : "json"}, defaults={"format" : "html"})
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function unattendAction($id)
	{
		$enityManager = $this->getDoctrine()->getManager();
		/** @var Event $event */
		$event = $enityManager->getRepository('EventBundle:Event')->find($id);

		if (!$event) {
			throw $this->createNotFoundException(sprintf('No event found for ID %d', $id));
		}

		if ($event->hasAttendee($this->getUser())) {
			$event->getAttendees()->removeElement($this->getUser());
			$enityManager->persist($event);
			$enityManager->flush();
		}

		$url = $this->generateUrl('event_show', array(
			'slug'	=> $event->getSlug()
		));

		return $this->redirect($url);
	}

	public function _upcomingEventsAction($max = null)
	{
		$em = $this->getDoctrine()->getManager();
		$events = $em->getRepository('EventBundle:Event')->getUpcomingEvents($max);

		return $this->render('EventBundle:Event:_upcomingEvents.html.twig', array(
			'events' => $events
		));
	}
}
