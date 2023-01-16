<?php

namespace App\Repository;

use App\Entity\AnnualPermit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnualPermit>
 *
 * @method AnnualPermit|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnualPermit|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnualPermit[]    findAll()
 * @method AnnualPermit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnualPermitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnualPermit::class);
    }

    public function save(AnnualPermit $entity, bool $flush = false): AnnualPermit
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
        
        return $entity;
    }

    public function edit(AnnualPermit $entity, $inputs) : AnnualPermit
    {
        if (!empty($inputs['start_date'])) {
            $startDate = date_create_from_format('d/m/Y',$inputs['start_date']);
            $startDate->setTime(0,0,0);
            $entity->setStartDate($startDate);
        }
        if (!empty($inputs['last_name'])) {
            $endDate = date_create_from_format('d/m/Y',$inputs['end_date']);
            $endDate->setTime(0,0,0);
            $entity->setEndDate($endDate);
        }

        $entity->setChangedBy('default_user'); // This place will updated when users will be added.
        $entity->setChangedAt(new \DateTime());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

}
