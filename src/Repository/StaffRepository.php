<?php

namespace App\Repository;

use App\Entity\Staff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Staff>
 *
 * @method Staff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Staff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Staff[]    findAll()
 * @method Staff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Staff::class);
    }

    public function save(Staff $entity, bool $flush = false): Staff
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function edit(Staff $entity, $inputs): Staff
    {
        if (!empty($inputs['first_name'])) {
            $entity->setFirstName($inputs['first_name']);
        }
        if (!empty($inputs['last_name'])) {
            $entity->setLastName($inputs['last_name']);
        }
        if (!empty($inputs['work_start_date'])) {
            $workStartDate = date_create_from_format('d/m/Y',$inputs['work_start_date']);
            $workStartDate->setTime(0,0,0);
            $entity->setWorkStartDate($workStartDate);
        }
        if (!empty($inputs['work_left_date'])) {
            $workLeftDate = date_create_from_format('d/m/Y',$inputs['work_left_date']);
            $workLeftDate->setTime(0,0,0);
            $entity->setWorkLeftDate($workLeftDate);
        }
        if (!empty($inputs['social_security_number'])) {
            $entity->setSocialSecurityNumber($inputs['social_security_number']);
        }
        if (!empty($inputs['citizenship_number'])) {
            $entity->setCitizenshipNumber($inputs['citizenship_number']);
        }

        $entity->setChangedBy('default_user'); // This place will updated when users will be added.
        $entity->setChangedAt(new \DateTime());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    public function filter($inputs)
    {
        $name = $inputs['name']??"";
        $permitStatus = $inputs['permit_status']??true;
        $startDate = $inputs['start_date']??"";
        $startDate = date_create_from_format('d-m-Y', $inputs['start_date']);
        $startDate->setTime(0,0,0);
        $endDate = $inputs['end_date']??"";
        $endDate = date_create_from_format('d-m-Y', $inputs['end_date']);
        $endDate->setTime(23,59,59);

        $qb = $this->createQueryBuilder('s')
        ->leftJoin('s.annualPermits', 'ap')
        ->where('s.deleted = 0');

        if ($inputs['start_date'] != "") {
            $qb->andWhere('ap.startDate >= :startDate')
            ->setParameter('startDate',$startDate);
        }
        if ($inputs['end_date'] != "") {
            $qb->andWhere('ap.endDate <= :endDate')
            ->setParameter('endDate',$endDate);
        }
        if ($name != "") {
            $qb->andWhere("CONCAT(s.firstName,' ',s.lastName) LIKE :name")
            ->setParameter('name',"%$name%");
        }

        if ((bool)$permitStatus) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->createQueryBuilder('s')
            ->where('s.id NOT IN (:permittedStaffIds)')
            ->setParameter('permittedStaffIds', $qb->getQuery()->getResult())
            ->getQuery()->getResult();
        }

    }

}
