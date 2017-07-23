<?php

namespace PatientBundle\Controller;


use PatientBundle\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Console\Exception\InvalidArgumentException;
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
        return $this->render('PatientBundle:Register:add.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/remove")
     */
    public function removeAction()
    {
        return $this->render('PatientBundle:Register:remove.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/selectHour/{year}/{month}/{day}/{noDay}", name="selectHour")
     */
    public function selectHourAction(Request $request, $year, $month, $day, $noDay)
    {


        $month = str_split($month);
        if ($month[0] == 0) {
            $month[0] = '';
        }
        $month = implode('', $month);

        $session = $request->getSession();
        $session->set('year', $year);
        $session->set('month', $month);
        $session->set('day', $day);
        $session->set('noDay', $noDay);
        var_dump($_SESSION);

        $calendarRepository = $this->getDoctrine()->getRepository('PatientBundle:Appointment');
        $visits = $calendarRepository->findAll();

        if (!$visits) {
            throw new NotFoundHttpException('Błąd połączenia z baza danych');
        }

        $em = $this->getDoctrine()->getManager();
        $daySchedule = $em->getRepository('PatientBundle:Appointment')->findDay($year, $month, $day);

        $diabArray = [];
        $dietArray = [];

        foreach ($daySchedule as $value) {
            if ($value->getVisitType() == 'diab') {
                $diabArray[] = $value->getHour();
            } else {
                $dietArray[] = $value->getHour();
            }

        }

        $visitType = $session->get('visitType');

        return $this->render('PatientBundle:Register:selectHour.html.twig', array(
            'daySchedule' => $daySchedule,
            'noDay' => $noDay,
            'visitType' => $visitType,
            'diabArray' => $diabArray,
            'dietArray' => $dietArray,
            'day' => $day,
            'month' => $month,
            'year' => $year
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

        return $this->render('PatientBundle:Register:selectVisitType.html.twig', array(// ...
        ));
    }

    /**
     * @Route("patientData/{hour}", name="patientData")
     */
    public function patientDataAction(Request $request, $hour)
    {
        $session = $request->getSession();
        $session->set('hour', $hour);

        return $this->render('PatientBundle:Register:patientData.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/patientDataConfirmation")
     */
    public function patientDataConfirmationAction(Request $request)
    {
        if (!$request->request->get('name') || !$request->request->get('surname') || !$request->request->get('phone')) {
            throw new InvalidArgumentException("Nie podano wszystkich danych lub podane dane są niewłaściwe!");
        }

        $session = $request->getSession();
        $session->set('name', $request->request->get('name'));
        $session->set('surname', $request->request->get('surname'));
        $session->set('phone', $request->request->get('phone'));
        $phone = $request->request->get('phone');
        $randomNumber = mt_rand(100000, 999999);
        var_dump($randomNumber);
        $session->set('code', $randomNumber);

        mail(
            '+48' . $phone . '@text.plusgsm.pl',
            '',
            'Kod: ' . $randomNumber,
            "From: dr Manuela Drozd-Sypien"
        );

        return $this->render('PatientBundle:Register:phoneConfirmation.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/saveVisit")
     */
    public function saveVisitAction(Request $request)
    {
        $code = $request->request->get('code');
        $session = $request->getSession();
        var_dump($code);
        var_dump($session->get('code'));

        if ($code == $session->get('code')) {
            return new Response("YEAHHHHHHHHHHH");
        } else {
            return new Response("NOOOOOOOOOOOOO");
        }
    }

}
