<?php

namespace PatientBundle\Controller;

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
     * @Route("/addVisit")
     */
    public function addVisitAction()
    {
        return $this->render('PatientBundle:AdminPanel:add_visit.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/cancelVisit")
     */
    public function cancelVisitAction()
    {
        return $this->render('PatientBundle:AdminPanel:cancel_visit.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/deleteOldVisits")
     */
    public function deleteOldVisitsAction()
    {
        return $this->render('PatientBundle:AdminPanel:delete_old_visits.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/admin")
     */
    public function adminAction()
    {
        return $this->render('PatientBundle:AdminPanel:admin.html.twig', array(
        // ...
        ));
    }

}
