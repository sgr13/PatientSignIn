<?php

namespace PatientBundle\Controller;


use PatientBundle\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        return $this->render('PatientBundle:Register:selectDay.html.twig', array(
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

    /**
     * @Route("/selectHour/{year}/{month}/{day}/{noDay}", name="selectHour")
     */
    public function selectHourAction(Request $request, $year, $month, $day, $noDay)
    {
//        var_dump($year);
//        var_dump($month);
//        var_dump($day);
//        var_dump($noDay);

        $month = str_split($month);
        if ($month[0] == 0) {
            $month[0] = '';
        }
        $month = implode('', $month);

        $calendarRepository = $this->getDoctrine()->getRepository('PatientBundle:Appointment');
        $visits = $calendarRepository->findAll();

        if (!$visits) {
            throw new NotFoundHttpException('Błąd połączenia z baza danych');
        }

        $em = $this->getDoctrine()->getManager();
        $daySchedule = $em->getRepository('PatientBundle:Appointment')->findDay($year, $month, $day);

        $diabArray = [];
        $dietArray = [];

        foreach($daySchedule as $value) {
            if ($value->getVisitType() == 'diab') {
                $diabArray[] = $value->getHour();
            } else {
                $dietArray[] = $value->getHour();
            }

        }
        $session = $request->getSession();
        $visitType = $session->get('visitType');

        return $this->render('PatientBundle:Register:selectHour.html.twig', array(
            'daySchedule' => $daySchedule,
            'noDay' => $noDay,
            'visitType' => $visitType,
            'diabArray' => $diabArray,
            'dietArray' => $dietArray
        ));
    }

    /**
     * @Route("/selectVisitType")
     */
    public function selectVisitTypeAction(Request $request)
    {
        if ($request->request->get('visitType')) {
            $visitType = $request->request->get('visitType');
            $session = $request->getSession();
            $session->set('visitType', $visitType);
            return $this->redirect('selectDay');
        }

        return $this->render('PatientBundle:Register:selectVisitType.html.twig', array(
            // ...
        ));
    }

}
