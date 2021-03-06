<?php

namespace PatientBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BlockDayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockDayRepository extends EntityRepository
{
    public function getDay($year, $month, $day)
    {
        $q = $this->createQueryBuilder('v');

        $q->select('v')
            ->where('v.year = :year', 'v.month = :month', 'v.day = :day')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('day', $day);

        return $q->getQuery()->getOneOrNullResult();
    }
}
