<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\SizeChart;

class SizeChartController extends Controller
{
    protected $sizeChartModel;

    public function __construct(SizeChart $size_chart)
    {
        $this->sizeChartModel = $size_chart;
    }

    public function index()
    {
        $limit = 5;
        $page = get_query("page");
        $page = ($page && is_numeric($page)) ? $page : 1;
        $keyword = get_query("keyword") ?? null;
        list($size_charts,$number_of_size_charts) = $this->sizeChartModel->getList($page,$limit,$keyword);
        $totalPages = ceil($number_of_size_charts / $limit);
        return view("admin/size_chart/index",array("size_charts" => $size_charts,"totalPages"=>$totalPages,"currentPage"=>$page));
    }

    public function addView()
    {
        return view("admin/size_chart/add");
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            "name" => "bail|required|unique:size_charts",
            "size-chart" => "bail|required|json"
        ]);
        $this->sizeChartModel->saveSizeChart($validated);
        return response()->flash("success","Tạo bảng kích cỡ thành công")
                        ->json(["back"=>url("admin/size-chart")]);
    }

    public function edit($id)
    {
        $size_chart = $this->sizeChartModel->getSizeChart($id);
        if(!$size_chart){
            return;
        }
        return view("admin/size_chart/edit",array("size_chart"=>$size_chart));
    }

    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            "name" => "bail|required|unique:size_charts,name,$id",
            "size-chart" => "bail|required|json"
        ]);
        $size_chart = $this->sizeChartModel->first(where:array("id" => $id));
        if(!$size_chart){
            return;
        }
        $size_chart->updateSizeChart($validated);
        return response()->json(["message"=>"Cập nhật bảng kích cỡ thành công"]);
    }

    public function delete($id)
    {
        $size_chart = $this->sizeChartModel->first(where:array("id"=>$id));
        if($size_chart){
            $size_chart->deleteSizeChart();
        }
        return response()->back()->with("success","Xóa bảng kích cỡ thành công");
    }
}