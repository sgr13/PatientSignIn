<?php

namespace PatientBundle\Controller;

use PatientBundle\Entity\BlockDay;
use PatientBundle\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminPanelController extends Controller
{
    /**
     * @Route("/showAll")
     */
    public function showAllAction(Request $request)
    {
        $visitYear = date('Y');
        $visitMonth = date('n');
        $visitDay = date('j');

        $em = $this->getDoctrine()->getManager();
        $days = [];
        $visits = $em->getRepository('PatientBundle:Appointment')->getVisits($visitYear, $visitMonth, $days);

        if ($request->request->get('selectMonth') || $request->request->get('selectYear') || $request->request->get('selectDay')) {
            $visitMonth = $request->request->get('selectMonth');
            $visitYear = $request->request->get('selectYear');
            $visitDay = $request->request->get('selectDay');

            $visitByMonth = $em->getRepository('PatientBundle:Appointment')->getVisitsByMonth($visitYear, $visitMonth, $visitDay);
            $days = [];
            $visits = $em->getRepository('PatientBundle:Appointment')->getVisits($visitYear, $visitMonth, $days);

            return $this->render('PatientBundle:AdminPanel:show_all.html.twig', array(
                'visitMonth' => $visitMonth,
                'days' => $visits,
                'visit' => $visitByMonth,
                'visitYear' => $visitYear,
                'visitDay' => $visitDay
            ));
        }
        return $this->render('PatientBundle:AdminPanel:show_all.html.twig', array(
            'visitMonth' => $visitMonth,
            'days' => $visits,
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
        $visit = $em->getRepository('PatientBundle:Appointment')->getVisitByHour($year, $month, $day, $hour);

        if (!$visit) {
            return $this->render('PatientBundle:AdminPanel:cancel_visit.html.twig', array());
        }
        $em->remove($visit);
        $em->flush();

        return $this->render('PatientBundle:AdminPanel:cancel_visit.html.twig', array(
            'visit' => $visit
        ));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render('PatientBundle:AdminPanel:admin.html.twig', array());
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
        $month = $em->getRepository('PatientBundle:Appointment')->getChangedDigit($month);
        $daySchedule = $em->getRepository('PatientBundle:Appointment')->getDay($year, $month, $day);

        if ($daySchedule) {
            return $this->render('PatientBundle:AdminPanel:blockedDay.html.twig', array());
        }

        $dayToUnblock = $em->getRepository('PatientBundle:BlockDay')->getDay($year, $month, $day);

        if ($dayToUnblock) {
            $em->remove($dayToUnblock);
            $em->flush();
            
            return $this->render('PatientBundle:AdminPanel:blockedDay.html.twig', array(
                'dayToUnblock' => $dayToUnblock
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
