<?php

namespace App\Service;

use App\Entity\Customer;
use App\Http\RandomUserApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RandomUserApiDataManager implements DataPersistInterface
{
    /** @var RandomUserApiClient */
    private $randomUserApiClient;

    /** @var EntityManagerInterface */
    private $entityManager;

    private $usersCreated = [];

    private $usersUpdated = [];

    public function __construct(EntityManagerInterface $entityManager, RandomUserApiClient $randomUserApiClient)
    {
        $this->entityManager = $entityManager;
        $this->randomUserApiClient = $randomUserApiClient;
    }

    public function persistRandomUserApi($noOfResults = 100, $nationality = 'AU')
    {
        //RandomUserApiClient setter
        $this->randomUserApiClient->setNoOfResults($noOfResults);
        $this->randomUserApiClient->setNationality($nationality);

        //RandomUserApiClient result
        try {
            $randomUserApiData = $this->randomUserApiClient->fetchApiData();
            $statusCode = $randomUserApiData->getStatusCode();

            if ($statusCode == 200) {

                $this->persistData($randomUserApiData->getContent());

            }else{
                throw new \Exception('Failed to fetch data from API [Error ' . $statusCode . ']');
            }
        } catch (\Exception|TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }

    public function usersCreated(): array
    {
        return $this->usersCreated;
    }

    public function usersUpdated(): array
    {
        return $this->usersUpdated;
    }

    public function persistData($apiData)
    {
        $rawData = json_decode($apiData);

        try{
            foreach($rawData->results as $result) {
                $email = $result->email;
                $customer = $this->getCustomerByEmail($email);

                $customer
                    ->setFirstname($result->name->first)
                    ->setLastname($result->name->last)
                    ->setEmail($email)
                    ->setUsername($result->login->username)
                    ->setPassword($result->login->password)
                    ->setGender($result->gender)
                    ->setPhone($result->phone)
                    ->setCity($result->location->city)
                    ->setCountry($result->location->country);

                $this->entityManager->persist($customer);
            }

            $this->entityManager->flush();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getCustomerByEmail($customer_email): Customer
    {
        $repo = $this->entityManager->getRepository(Customer::class);

        $existingUser = $repo->findOneBy(['email' => $customer_email]);

        if ($existingUser) {
            $this->usersUpdated[] = $customer_email;
            return $existingUser;
        } else {
            $this->usersCreated[] = $customer_email;
            return new Customer();
        }
    }
}