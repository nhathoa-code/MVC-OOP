<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\SizeChart;
use NhatHoa\App\Repositories\Interfaces\SizeChartRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class SizeChartRepository extends BaseRepository implements SizeChartRepositoryInterface
{
    public function getAll($page, $limit, $keyword) : array
    {
        if($keyword){
            $query = SizeChart::where("name","like","%{$keyword}%");
        }else{
            $query = SizeChart::orderBy("id","desc");
        }
        $number_of_size_charts = $query->count(false);
        $size_charts = $query->limit($limit)->offset(($page - 1) * $limit)->get();
        return array($size_charts,$number_of_size_charts);
    }

    public function getById($id) : SizeChart|null
    {
        $size_chart = SizeChart::first(where:array("id"=>$id));
        return $size_chart;
    }

    public function create($validated) : void
    {
        $sizeChart = new SizeChart();
        $sizeChart->name = $validated['name'];
        $sizeChart->chart = $validated['size-chart'];
        $sizeChart->save();
    }

    public function update(SizeChart $sizeChart,$validated) : void
    {
        $sizeChart->name = $validated['name'];
        $sizeChart->chart = $validated['size-chart'];
        $sizeChart->save();
    }

    public function delete(SizeChart $sizeChart) : void
    {
        $sizeChart->delete();
    }
}