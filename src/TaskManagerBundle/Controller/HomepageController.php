<?php

namespace TaskManagerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class HomepageController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new RedirectResponse($this->generateUrl('task_index'));
    }

    /**
     * @Route("/change_password_confirmation", name="change_password_confirmation")
     */
    public function changePasswordConfirmationAction(Request $request)
    {
        return $this->render('FOSUserBundle:ChangePassword:changePasswordConfirmation.html.twig');
    }

}
