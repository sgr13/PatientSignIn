<?php

namespace PatientBundle\Controller;

use PatientBundle\Entity\BlockDay;
use PatientBundle\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminPanelController extends Controller
{
    /**
     * @Route("/showAll")
     */
    public function showAllAction(Request $request)
    {
        $visitYear = 2017;
        $visitMonth = date('n');
        $visitDay = date('j');
        $em = $this->getDoctrine()->getManager();
        $visits = $em->getRepository('PatientBundle:Appointment')->findVisits($visitYear, $visitMonth);
        $days = [];
        foreach($visits as $visit) {
            if (!in_array($visit->getDay(), $days)) {
                $days[] = $visit->getDay();
            }
        }
        sort($days);
        if ($request->request->get('selectMonth') || $request->request->get('selectYear') || $request->request->get('selectDay')) {
            $visitMonth = $request->request->get('selectMonth');
            $visitYear = $request->request->get('selectYear');
            $visitDay = $request->request->get('selectDay');
            $visita = $em->getRepository('PatientBundle:Appointment')->findVisitsByMonth($visitYear, $visitMonth, $visitDay);
            $visits = $em->getRepository('PatientBundle:Appointment')->findVisits($visitYear, $visitMonth);
            $days = [];
            foreach($visits as $visit) {
                if (!in_array($visit->getDay(), $days)) {
                    $days[] = $visit->getDay();
                }
            }
            sort($days);

            return $this->render('PatientBundle:AdminPanel:show_all.html.twig', array(
                'visitMonth' => $visitMonth,
                'visits' => $visits,
                'days' =>$days,
                'visit' => $visita,
                'visitYear' => $visitYear,
                'visitDay' => $visitDay
            ));
        }
        return $this->render('PatientBundle:AdminPanel:show_all.html.twig', array(
            'visitMonth' => $visitMonth,
            'visits' => $visits,
            'days' =>$days,
            'visitYear' => $visitYear,
            'visitDay' => $visitDay
        ));
    }

    /**
     * @Route("/cancelVisit/{hour}/{day}/{month}/{year}", name="cancelVisit")
     */
    public function cancelVisitAction(Request $request, $hour, $day, $month, $year)
    {
        $em = $this->getDoctrine()->getManager();
        $visit = $em->getRepository('PatientBundle:Appointment')->findVisitByHour($year, $month, $day, $hour);
        $em->remove($visit[0]);
        $em->flush();
        return $this->render('PatientBundle:AdminPanel:cancel_visit.html.twig', array(
            'visit' => $visit[0]
        ));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render('PatientBundle:AdminPanel:admin.html.twig', array(
        // ...
        ));
    }

    /**
     * @Route("/blockVisit")
     */
    public function blockVisitAction(Request $request)
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
        return $this->render('PatientBundle:AdminPanel:block_visit.html.twig', array(
            'calendar' => $calendar
        ));
    }

    /**
     * @Route("/blockDay/{year}/{month}/{day}", name="blockDay")
     */
    public function blockDayAction(Request $request, $year, $month, $day)
    {
        $em = $this->getDoctrine()->getManager();
        $month = str_split($month);
        if ($month[0] == 0) {
            $month[0] = '';
        }
        $month = implode('', $month);

        if ($daySchedule = $em->getRepository('PatientBundle:Appointment')->findDay($year, $month, $day)) {
            die("Nie można zablokowac dnia ze względu na zaplanowane wizyty! Usuń wszystkie wizyty z tego dnia");
        }

        if ($em->getRepository('PatientBundle:BlockDay')->findDay($year, $month, $day)) {
            $dayToUnblock = $em->getRepository('PatientBundle:BlockDay')->findDay($year, $month, $day);
            $em->remove($dayToUnblock[0]);
            $em->flush();
            return $this->render('PatientBundle:AdminPanel:blockedDay.html.twig', array(
                'dayToUnblock' => $dayToUnblock[0]
            ));
        }

        $dayBlocked = new BlockDay();
        $dayBlocked->setYear($year);
        $dayBlocked->setMonth($month);
        $dayBlocked->setDay($day);

        $em->persist($dayBlocked);
        $em->flush();

        return $this->render('PatientBundle:AdminPanel:blockedDay.html.twig', array(
            'day' => $dayBlocked
        ));
    }
}
