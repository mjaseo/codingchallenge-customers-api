<?php

namespace App\Tests;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
       $kernel = self::bootKernel();

       DatabasePrimer::prime($kernel);

       $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @test
     */
    public function a_create_record_or_find_existing_customer()
    {
        $customerRepository = $this->entityManager->getRepository(Customer::class);

        $customerRecord = $customerRepository->findOneBy(['username' => 'bigtiger435']);

        $customer = $customerRecord ?? new Customer();

        $customer
            ->setFirstName('Karl-Dieter')
            ->setLastName('Schwenke')
            ->setEmail('karl-dieter.schwenke@example.com')
            ->setUsername('bigtiger435')
            ->setPassword('byebye')
            ->setGender('male')
            ->setCountry('Germany')
            ->setCity('Leonberg')
            ->setPhone('0488-1225563');

        $this->entityManager->persist($customer);

        $this->entityManager->flush();
dd($customer);
        $this->assertEquals('Karl-Dieter Schwenke', $customer->getFullName());
        $this->assertEquals(md5('byebye'), $customer->getPassword());
        $this->assertEquals('karl-dieter.schwenke@example.com', $customer->getEmail());
        $this->assertEquals('bigtiger435', $customer->getUsername());
        $this->assertEquals('male', $customer->getGender());
    }
}