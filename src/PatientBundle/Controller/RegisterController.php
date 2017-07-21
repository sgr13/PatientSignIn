<?php

namespace PatientBundle\Controller;


use PatientBundle\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * @Route("/selectDay")
     */
    public function selectDayAction(Request $request)
    {
        $calendar = new Calendar();
        $selectedMonth = $calendar->getMonth();
        $selectedYear = $calendar->getYear();

        if ($request->request->get('selectMonth')) {
            $selectedMonth = $request->request->get('selectMonth');
        }

        if ($request->request->get('selectYear')) {
            $selectedYear = $request->request->get('selectYear');
        }
        $calendar->setMonth($selectedMonth);
        $calendar->setYear($selectedYear);
        return $this->render('PatientBundle:Register:show.html.twig', array(
            'calendar' => $calendar
        ));
    }

    /**
     * @Route("/add")
     */
    public function addAction()
    {
        return $this->render('PatientBundle:Register:add.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/remove")
     */
    public function removeAction()
    {
        return $this->render('PatientBundle:Register:remove.html.twig', array(
            // ...
        ));
    }

//    /**
//     * @Route("/ajax", name="_recherche_ajax")
//     */
//    public function ajaxAction(Request $request)
//    {
//        if ($request->isXMLHttpRequest()) {
//            return new JsonResponse(array('data' => 'this is a json response'));
//        }
//
//        return new Response('This is not ajax!', 400);
//    }

}
