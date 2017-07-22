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

    /**
     * @Route("/selectVisitType/{year}/{month}/{day}", name="selectVisitType")
     */
    public function selectVisitTypeAction(Request $request, $year, $month, $day)
    {
        var_dump($year);
        var_dump($month);
        var_dump($day);

        $month = str_split($month);
        if ($month[0] == 0) {
            $month[0] = '';
        }
        $month = implode('', $month);

        $calendarRepository = $this->getDoctrine()->getRepository('PatientBundle:Appointment');
        $visits = $calendarRepository->findAll();
        var_dump($visits);

        if (!$visits) {
            throw new NotFoundHttpException('Błąd połączenia z baza danych');
        }

        $em = $this->getDoctrine()->getManager();
        $daySchedule = $em->getRepository('PatientBundle:Appointment')->findDay($year, 7, $day);
        var_dump($daySchedule);

        return $this->render('PatientBundle:Register:selectVisitType.html.twig', array(
            // ...
        ));
    }

}
