<?php

namespace App\Services;

use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddressService
{

    public function __construct(private EntityManagerInterface $entityManager,private AddressRepository $addressRepository)
    {
    }

    public function findAll(): array
    {
        return $this->addressRepository->findAll();
    }

    public function findById(int $id)
    {
        return $this->addressRepository->find($id);
    }



}
