<?php

namespace AppBundle\Controller;

//use AppBundle\AppBundle;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/create", name="new_task")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LogicException
     */
    public function createAction(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('show_task', [
                'id' => $task->getId()
            ]);
        }

        return $this->render(':task:create.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show_task")
     * @Method("GET")
     * @param Task $task
     * @return Response
     */
    public function showAction(Task $task): Response
    {
        $deleteForm = $this->createDeleteFrom($task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    public function editAction()
    {
        // wpisać kod ...
    }

    /**
     * @Route("/{id}", name="delete_task")
     * @Method("DELETE")
     * @param Request $request
     * @param Task $task
     * @return string
     */
    public function deleteAction(Request $request, Task $task)
    {
        $form = $this->createDeleteFrom($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($task);
            $em->flush();
        }

        return 'OK';// zmienic OK na kod właściwy ...
    }

    /**
     * @param Task $task
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteFrom(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('show_task', ['id' => $task->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
