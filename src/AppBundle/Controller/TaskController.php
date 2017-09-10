<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/create", name="new_task")
     * @Method("GET", "POST")
     */
    public function createAction(Request $request)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task/{id}', array('id' => $task->getId()));
        }

        return []; # Dopisać
    }


    public function showAction(Request $request)
    {
        # Dodać kod
    }

}
