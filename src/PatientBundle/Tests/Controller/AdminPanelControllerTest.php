<?php

namespace PatientBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPanelControllerTest extends WebTestCase
{
    public function testShowall()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showAll');
    }

    public function testAddvisit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addVisit');
    }

    public function testCancelvisit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cancelVisit');
    }

    public function testDeleteoldvisits()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deleteOldVisits');
    }

}
