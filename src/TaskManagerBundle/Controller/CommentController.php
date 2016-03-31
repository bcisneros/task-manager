<?php

namespace TaskManagerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TaskManagerBundle\Entity\Comment;
use TaskManagerBundle\Entity\Task;

/**
 * Comment controller.
 *
 * @Route("/tasks/{task_id}/comments")
 * @ParamConverter("task", class="TaskManagerBundle:Task", options={"mapping": {"task_id": "id"}})
 */
class CommentController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     * @Route("/new", name="comment_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Task $task)
    {
        $comment = new Comment();
        $comment->setTask($task);
        $form = $this->createForm('TaskManagerBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('task_show', array('id' => $task->getId()));
        }

        return $this->render('comment/new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

}
