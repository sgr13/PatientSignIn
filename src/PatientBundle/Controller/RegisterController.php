<?php

namespace PatientBundle\Controller;


use PatientBundle\Entity\Appointment;
use PatientBundle\Entity\Calendar;
use PatientBundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;

class RegisterController extends Controller
{
    /**
     * @Route("/selectVisitType", name="selectVisitType")
     */
    public function selectVisitTypeAction(Request $request)
    {
        if ($request->request->get('visitType')) {

            $visitType = $request->request->get('visitType');
            $session = $request->getSession();
            $session->set('visitType', $visitType);

            return $this->redirect('selectDay');
        }
        return $this->render('PatientBundle:Register:selectVisitType.html.twig', array());
    }

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
     * @Route("/selectHour/{year}/{month}/{day}/{noDay}", name="selectHour")
     */
    public function selectHourAction(Request $request, $year, $month, $day, $noDay)
    {
        $em = $this->getDoctrine()->getManager();
        $month = $em->getRepository('PatientBundle:Appointment')->getChangedDigit($month);

        if ($em->getRepository('PatientBundle:BlockDay')->getDay($year, $month, $day)) {

            return $this->render('PatientBundle:Register:changeVisitDay.html.twig', array());
        }

        $session = $request->getSession();
        $session->set('year', $year);
        $session->set('month', $month);
        $session->set('day', $day);
        $session->set('noDay', $noDay);

        $daySchedule = $em->getRepository('PatientBundle:Appointment')->getVisitTypes($year, $month, $day);
        $visitType = $session->get('visitType');

        $array = [
            'daySchedule' => $daySchedule,
            'noDay' => $noDay,
            'visitType' => $visitType,
            'diabArray' => $daySchedule[0],
            'dietArray' => $daySchedule[1],
            'day' => $day,
            'month' => $month,
            'year' => $year
        ];

        return $this->render('PatientBundle:Register:selectHour.html.twig', $array);
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
        $session->set('code', $randomNumber);
        echo "Kod: " . $randomNumber;

//        mail(
//            '+48' . $phone . '@text.plusgsm.pl',
//            '',
//            'Kod: ' . $randomNumber,
//            "From: drManuelaDrozd-Sypien"
//        );

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

        if ($code == $session->get('code')) {
            $appointment = new Appointment();

            $appointment->setPhone($session->get('phone'));
            $appointment->setYear($session->get('year'));
            $appointment->setMonth($session->get('month'));
            $appointment->setDay($session->get('day'));
            $appointment->setDayOfWeek($session->get('noDay'));
            $appointment->setHour($session->get('hour'));
            $appointment->setName($session->get('name'));
            $appointment->setSurname($session->get('surname'));
            $appointment->setVisitType($session->get('visitType'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

            $em->getRepository('PatientBundle:Appointment')->sendMail($appointment->getDay(), $appointment->getMonth(), $appointment->getYear(), $appointment->getHour(), $appointment->getSurname(), $appointment->getName(), $appointment->getPhone(), $appointment->getVisitType(), 'Nowa Wizyta');

            return $this->render('PatientBundle:Register:visitSummary.html.twig', array(
                'appointment' => $appointment
            ));

        } else {
            return $this->render('PatientBundle:Register:visitSummary.html.twig', array());
        }
    }

    /**
     * @Route("cancelVisit")
     */
    public function cancelVisitAction(Request $request)
    {
        $randomNumber = null;

        if ($request->request->get('phone')) {
            $session = $request->getSession();
            $session->set('phone', $request->request->get('phone'));
            $randomNumber = mt_rand(100000, 999999);
            $session->set('code', $randomNumber);
            echo "Kod: " . $randomNumber;
        }

        return $this->render('PatientBundle:Register:cancelVisit.html.twig', array(
            'randomNumber' => $randomNumber
        ));
    }

    /**
     * @Route("cancelVisitConfirm")
     */
    public function cancelVisitConfirmAction(Request $request)
    {
        $session = $request->getSession();
        $code = $session->get('code');

        if ($code != $request->request->get('code')) {
            die ("Podano niewłaściwy kod");
        }

        $phone = $session->get('phone');
        $em = $this->getDoctrine()->getManager();
        $visits = $em->getRepository('PatientBundle:Appointment')->findByPhone($phone);

        if (!$visits) {
            return $this->render('PatientBundle:Register:cancelVisitConfirm.html.twig', array(
            ));
        }

        return $this->render('PatientBundle:Register:cancelVisitConfirm.html.twig', array(
            'visits' => $visits
        ));
    }

    /**
     * @Route("/cancelVisitFinal/{id}", name="cancelVisitFinal")
     */
    public function cancelVisitFinalAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $visit = $em->getRepository('PatientBundle:Appointment')->find($id);

        if (!$visit) {
            return $this->render('PatientBundle:Register:cancelVisitFinal.html.twig', array());
        }

        $em->getRepository('PatientBundle:Appointment')->sendMail($visit->getDay(), $visit->getMonth(), $visit->getYear(), $visit->getHour(), $visit->getSurname(), $visit->getName(), $visit->getPhone(), $visit->getVisitType(), 'Odwołana Wizyta');


        $em->remove($visit);
        $em->flush();

        return $this->render('PatientBundle:Register:cancelVisitFinal.html.twig', array(
            'visit' => $visit
        ));
    }
}
