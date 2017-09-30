<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="index_category")
     * @Method("GET")
     */
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['creator' => $this->getUser()]);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/create", name="new_category")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreator($this->getUser());

            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('index_category');
        }

        return $this->render('category/create.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show_category")
     * @Method("GET")
     */
    public function showAction(Category $category)
    {
        if ($this->getUser() !== $category->getCreator()) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($category);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="edit_category")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        if ($this->getUser() !== $category->getCreator()) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($category);

        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index_category');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
            'edit_form' => $editForm->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", name="delete_category")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('index_category');
    }

    /**
     * @param Category $category
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder($category)
            ->setAction($this->generateUrl('delete_category',['id' => $category->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm()
        ;

    }
}
