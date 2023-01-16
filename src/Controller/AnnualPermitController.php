<?php
namespace App\Controller;

use App\Entity\AnnualPermit;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerBuilder;

#[Route("/api/annual-permit")]
class AnnualPermitController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    )
    {}

    #[Route("/add", name:"add_annual_permit", methods:["POST"])]
    public function addAction(Request $request): Response
    {
        try {
            $staffRepo = $this->em->getRepository(Staff::class);
            $annualPermitRepo = $this->em->getRepository(AnnualPermit::class);
            $inputs = json_decode($request->getContent(),true);

            if (!empty($inputs)) {
                $staff = $staffRepo->find($inputs['staff_id']);

                $startDate = date_create_from_format('d/m/Y',$inputs['start_date']);
                $startDate->setTime(0,0,0);
                $endDate = date_create_from_format('d/m/Y',$inputs['end_date']);
                $endDate->setTime(0,0,0);

                $annualPermit = new AnnualPermit;
                $annualPermit->setStaff($staff);
                $annualPermit->setStartDate($startDate);
                $annualPermit->setEndDate($endDate);
                $annualPermit->setCreatedBy('default_user'); // This place will updated when users will be added.
                $annualPermit->setCreatedAt(new \DateTime());
    
                $annualPermitSaved = $annualPermitRepo->save($annualPermit, true);
    
                $jsonContent = SerializerBuilder::create()->build()->serialize($annualPermitSaved, 'json');
                
                return new JsonResponse(["id" => $annualPermitSaved->getId(), "annual_permit" => json_decode($jsonContent,true), "status" => 'success']);
            }
        } catch (Exception $e) {
            return new JsonResponse(["id" => null, "annual_permit" => [], "status" => 'error', "error_messsage" => $e->getMessage()]);
        }
    }

    #[Route("/delete/{id}", name:"delete_annual_permit", methods:["DELETE"])]
    public function deleteAction($id, Request $request)
    {
        try {
            $annualPermitRepo = $this->em->getRepository(AnnualPermit::class);
            $annualPermit = $annualPermitRepo->find($id);
    
            if (!empty($annualPermit)) {
                $annualPermit->setDeleted(true);
                $annualPermit->setDeletedBy('default_user'); // This place will updated when users will be added.
                $annualPermit->setDeletedAt(new \DateTime());
                $annualPermitRepo->save($annualPermit, true);
            }
    
            return new JsonResponse(["id" => $id, "status" => 'success']);
        } catch (Exception $e) {
            return new JsonResponse(["status" => 'error', "error_messsage" => $e->getMessage()]);
        }
    }

    #[Route("/edit/{id}", name:"edit_annual_permit", methods:["PUT"])]
    public function editAction($id, Request $request)
    {
        try {
            $annualPermitRepo = $this->em->getRepository(AnnualPermit::class);
            $annualPermit = $annualPermitRepo->find($id);
            $inputs = json_decode($request->getContent(),true);

            if (!empty($inputs) && !empty($annualPermit)) {
                $annualPermitEdited = $annualPermitRepo->edit($annualPermit,$inputs);
            } else {
                throw new Exception("Parameters not available.");
            }

            $jsonContent = SerializerBuilder::create()->build()->serialize($annualPermitEdited, 'json');

            return new JsonResponse(["id" => $id, "annual_permit" => json_decode($jsonContent,true), "status" => 'success']);
        } catch (Exception $e) {
            return new JsonResponse(["status" => 'error', "annual_permit" => [], "error_messsage" => $e->getMessage()]);
        }
    }

}