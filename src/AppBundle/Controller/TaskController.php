<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $tasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(['creator' => $this->getUser()]);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/create", name="new_task")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws LogicException
     */
    public function createAction(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatorId($this->getUser());

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
        if ($this->getUser() !== $task->getCreator()) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteFrom($task);

        $statusForm = $this->createDoneForm($task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'delete_form' => $deleteForm->createView(),
            'status_form' => $statusForm->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_task")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, Task $task)
    {
        if ($this->getUser() !== $task->getCreator()) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteFrom($task);

        $editForm = $this->createForm(TaskType::class, $task);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_task', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);

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

        return $this->redirectToRoute('task_index');
    }

    /**
     * @param Task $task
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteFrom(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('show_task', ['id' => $task->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm()
        ;
    }

    /**
     * @Route("/status/{id}", name="status_task")
     * @Method("POST")
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function statusAction(Task $task)
    {
        $status = $task->getDone() ? false : true;

        $task->setDone($status);

        $em = $this->getDoctrine()->getManager();

        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('show_task', ['id' => $task->getId()]);
    }

    private function createDoneForm(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('status_task', [
                'id' => $task->getId(),
            ]))
            ->setMethod(Request::METHOD_POST)
            ->getForm()
        ;
    }

}
