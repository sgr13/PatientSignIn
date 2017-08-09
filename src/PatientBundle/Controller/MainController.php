<?php

namespace PatientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MainController extends Controller
{
    /**
     * @Route("/main", name="main")
     */
    public function mainAction()
    {
        return $this->render('PatientBundle:Main:main.html.twig', array(
            // ...
        ));
    }

}
