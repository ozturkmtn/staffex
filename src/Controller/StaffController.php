<?php
namespace App\Controller;

use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

#[Route("/staff")]
class StaffController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    )
    {}

    #[Route("/add", name:"add_staff", methods:["POST"])]
    public function addAction(Request $request): Response
    {
        try {
            $inputs = $request->request->all();

            if (!empty($inputs)) {
                $workStartDate = date_create_from_format('d/m/Y',$inputs['work_start_date']);
                $workStartDate->setTime(0,0,0);

                $staff = new Staff;
                $staff->setFirstName($inputs['first_name']);
                $staff->setLastName($inputs['last_name']);
                $staff->setWorkStartDate($workStartDate);
                $staff->setSocialSecurityNumber($inputs['social_security_number']);
                $staff->setCitizenshipNumber($inputs['citizenship_number']);
                $staff->setCreatedBy('default_user'); // This place will updated when users will be added.
                $staff->setCreatedAt(new \DateTime());
    
                $staffSaved = $this->em->getRepository(Staff::class)->save($staff, true);
    
                $staffSavedEncoded = json_encode($staffSaved);
                
                return new JsonResponse(["id" => $staffSaved->getId(), "staff" => $staffSavedEncoded, "status" => 'success']);
            }
        } catch (Exception $e) {
            return new JsonResponse(["id" => null, "staff" => [], "status" => 'error', "error_messsage" => $e->getMessage()]);
        }
    }

    #[Route("/delete/{id}", name:"delete_staff", methods:["DELETE"])]
    public function deleteAction($id, Request $request)
    {
        try {
            $staffRepo = $this->em->getRepository(Staff::class);
            $staff = $staffRepo->find($id);
    
            if (!empty($staff)) {
                $staff->setDeleted(true);
                $staff->setDeletedBy('default_user'); // This place will updated when users will be added.
                $staff->setDeletedAt(new \DateTime());
                $staffRepo->save($staff, true);
            }
    
            return new JsonResponse(["id" => $id, "status" => 'success']);
        } catch (Exception $e) {
            return new JsonResponse(["status" => 'error', "error_messsage" => $e->getMessage()]);
        }
    }

    #[Route("/edit/{id}", name:"edit_staff", methods:["PUT"])]
    public function editAction($id, Request $request)
    {
        try {
            $staffRepo = $this->em->getRepository(Staff::class);
            $staff = $staffRepo->find($id);
            $inputs = json_decode($request->getContent(),true);

            if (!empty($inputs) && !empty($staff)) {
                $staffEdited = $staffRepo->edit($staff,$inputs);
            } else {
                throw new Exception("Parameters not available.");
            }

            return new JsonResponse(["id" => $id, "status" => 'success']);
        } catch (Exception $e) {
            return new JsonResponse(["status" => 'error', "error_messsage" => $e->getMessage()]);
        }
    }

    #[Route("/filter", "staff_filter", methods:["GET"])]
    public function filterAction(Request $request)
    {
        $staffRepo = $this->em->getRepository(Staff::class);
        $inputs = $request->query->all();

        $filteredResult = $staffRepo->filter($inputs);

        $jsonContent = SerializerBuilder::create()->build()->serialize($filteredResult, 'json');

        return new JsonResponse(["result" => json_decode($jsonContent), "status" => 'success']);
    }

}