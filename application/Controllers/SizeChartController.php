<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Repositories\Interfaces\SizeChartRepositoryInterface;

class SizeChartController extends Controller
{
    protected $sizeChartRepository;

    public function __construct(SizeChartRepositoryInterface $sizeChartRepository)
    {
        $this->sizeChartRepository = $sizeChartRepository;
    }

    public function index(Request $request)
    {
        $page = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        list($size_charts,$number_of_size_charts) = $this->sizeChartRepository->getAll($page,5,$keyword);
        $totalPages = ceil($number_of_size_charts / 5);
        return view("admin/size_chart/index",array("size_charts"=>$size_charts,"totalPages"=>$totalPages,"currentPage"=>$page));
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
        $this->sizeChartRepository->create($validated);
        return response()->flash("success","Tạo bảng kích cỡ thành công")
                        ->json(["back"=>url("admin/size-chart")]);
    }

    public function edit($id)
    {
        $size_chart = $this->sizeChartRepository->getById($id);
        if(!$size_chart) return;
        return view("admin/size_chart/edit",array("size_chart"=>$size_chart));
    }

    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            "name" => "bail|required|unique:size_charts,name,$id",
            "size-chart" => "bail|required|json"
        ]);
        $size_chart = $this->sizeChartRepository->getById($id);
        if(!$size_chart) return;
        $this->sizeChartRepository->update($size_chart,$validated);
        return response()->json(["message"=>"Cập nhật bảng kích cỡ thành công"]);
    }

    public function delete($id)
    {
        $size_chart = $this->sizeChartRepository->getById($id);
        if($size_chart){
            $this->sizeChartRepository->delete($size_chart);
        }
        return response()->back()->with("success","Xóa bảng kích cỡ thành công");
    }
}