<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CustomersController extends AbstractController
{
    /**
     * @Route("/customers", name="customers_list")
     */
    public function index(): JsonResponse
    {
        $customers = $this->getDoctrine()->getRepository(Customer::class)->findAll();

        $response = array_map(function($customer){
            return [
                'fullName' => $customer->getFullName(),
                'email' => $customer->getEmail(),
                'country' => $customer->getCountry()
            ];
        }, $customers);

        return $this->json($response);
    }

    /**
     * @Route("/customers/{id}", name="customer_info", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Customer $customer */
        $customer = $entityManager->getRepository(Customer::class)->find($id);

        if (!$customer) {
            return $this->json('Customer not found with id : ' . $id, 404);
        } else {
            return $this->json([
                'fullName' => $customer->getFullName(),
                'email' => $customer->getEmail(),
                'username' => $customer->getUsername(),
                'gender' => $customer->getGender(),
                'country' => $customer->getCountry(),
                'city' => $customer->getCity(),
                'phone' => $customer->getPhone()
            ]);
        }
    }
}
