<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\SizeChart;

interface SizeChartRepositoryInterface
{
    public function getAll(int $page,int $limit,string $keyword) : array;  
    public function getById(int $id) : SizeChart|null;
    public function create(array $data) : void;
    public function update(SizeChart $sizeChart, array $data) : void;
    public function delete(SizeChart $sizeChart) : void;
} 