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
    //1. Pacjent wybiera rodzaj wizyty, jeżeli wybór został poprawnie dokonany
    // zostaje przekierowany do wyboru dnia wizyty.
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
        return $this->render('PatientBundle:Register:selectVisitType.html.twig', array(// ...
        ));
    }

    //2. Pacjent wybiera dzień wizyty - z pomocą przychodzi klasa Calendar
    // tworząca graficzną prezentację.
    /**
     * @Route("/selectDay")
     */
    public function selectDayAction(Request $request)
    {
        $calendar = new Calendar();
        $selectedMonth = $calendar->getMonth();
        $selectedYear = $calendar->getYear();

        // 2a. Jeżeli dokonano zmiany miesiąca, dane zostają zaktualizowane
        if ($request->request->get('selectMonth')) {
            $selectedMonth = $request->request->get('selectMonth');
        }

        // 2b. Jeżeli dokonano zmiany roku, dane zostają zaktualizowane
        if ($request->request->get('selectYear')) {
            $selectedYear = $request->request->get('selectYear');
        }
        $calendar->setMonth($selectedMonth);
        $calendar->setYear($selectedYear);
        return $this->render('PatientBundle:Register:selectDay.html.twig', array(
            'calendar' => $calendar
        ));
    }

    // 3.Pacjent wybiera godzinę wizyty. Należy wziąć pod uwagę fakt, iż wizyta dietetyczna trwa 60 minut
    // natomiast diabetologiczna 30 minut.
    /**
     * @Route("/selectHour/{year}/{month}/{day}/{noDay}", name="selectHour")
     */
    public function selectHourAction(Request $request, $year, $month, $day, $noDay)
    {
        $em = $this->getDoctrine()->getManager();
        // 3a. Zamieniamy zapis miesiąca z postaci np. 07 na 7
        $month = str_split($month);
        if ($month[0] == 0) {
            $month[0] = '';
        }
        $month = implode('', $month);

        //3b. Jeżeli dany dzień został zablokowany przez administartora i znajduje się
        // w bazie danych BlockDay, pacjent zostaje poinformowany o braku możliwości rejestracji.
        if ($em->getRepository('PatientBundle:BlockDay')->findDay($year, $month, $day)) {
            return $this->render('PatientBundle:Register:changeVisitDay.html.twig', array(// ...
            ));
        }

        // 3c. Zapisujemy otrzymane dane do sesji
        $session = $request->getSession();
        $session->set('year', $year);
        $session->set('month', $month);
        $session->set('day', $day);
        $session->set('noDay', $noDay);

        //3d. Odnajdujemy wszystkie wizyty w danym dniu a anstępnie tworzymy dwie tablice,
        // odpowiednio dla wizyt diabetologicznych i dietetycznych.
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

    //4. Zapisujemy w sesji wybraną przez pacjenta godzinę.
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

    //5. Potwierdzamy dane pacjenta wysyłając sms-a na podany numer telefonu(tylko PLUS w tej formie).
    // W tresci sms-a przekazujemy losowo wybraną liczbę.
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
        var_dump($randomNumber);

//        mail(
//            '+48' . $phone . '@text.plusgsm.pl',
//            '',
//            'Kod: ' . $randomNumber,
//            "From: drManuelaDrozd-Sypien"
//        );

        return $this->render('PatientBundle:Register:phoneConfirmation.html.twig', array(// ...
        ));
    }

    //6. Zapisujemy wszystkie dane do bazy danych jeżeli wprowadzony kod zgadza się z kodem wysłanym
    // w sms-ie.
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

            return $this->render('PatientBundle:Register:visitSummary.html.twig', array(
            'appointment' => $appointment
            ));

        } else {
            throw new ValidatorException("Podano niewłaściwy kod.");
        }
    }

    //7. W przypadku odowłania wizyty tworzymy formularz w którym wpisujemy nr telefonu a nastepnie
    // na wskazany nr wysyłany jest kod losowo wygenerowany
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
            var_dump($randomNumber);
        }

        return $this->render('PatientBundle:Register:cancelVisit.html.twig', array(
            'randomNumber' => $randomNumber
        ));
    }

    //8. Po wprowadzeniu kodu i jego weryfikacji wyświetlamy listę wizyt, które pacjent umówił
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
        $visits = $em->getRepository('PatientBundle:Appointment')->findVisitByPhone($phone);
        return $this->render('PatientBundle:Register:cancelVisitConfirm.html.twig', array(
            'visits' => $visits
        ));
    }

    //9. Po wybraniu jednej z wizyt, usuwamy ją z bazy danych
    /**
     * @Route("/cancelVisitFinal/{id}", name="cancelVisitFinal")
     */
    public function cancelVisitFinalAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $visit = $em->getRepository('PatientBundle:Appointment')->find($id);
        $date = $visit->getDay() . '/' . $visit->getMonth() . '/' . $visit->getYear();
        $hour = $visit->getHour();
        $patient = $visit->getSurname() . ' ' . $visit->getName();
        $phone = $visit->getPhone();
        $visitType = $visit->getVisitType();
        mail('s.g.jarzabek@gmail.com',
            'Odwołana wizyta!!!',
            'Data:' . $date . ' | ' .
            'Godzina: ' . $hour . ' | ' .
            'Pacjent: ' . $patient . ' | ' .
            'Telefon: ' . $phone . ' | ' .
            'Wizyta: ' . $visitType
            );
        $em->remove($visit);
        $em->flush();

        return $this->render('PatientBundle:Register:cancelVisitFinal.html.twig', array(
            'visit' => $visit
        ));
    }
}
