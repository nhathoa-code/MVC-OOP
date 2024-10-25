<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function getAll(int $currentPage, int $limit, string $keyword) : array;
    public function getById(int $id) : Customer|null;
    public function getByPhoneNumber(string $phone_number) : Customer|null;
    public function create(array $data) : void;
    public function update(Customer $customer, array $data) : void;
    public function delete(Customer $customer) : void;
} 