<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Province;

class LocationController extends Controller
{
    protected $provinceModel;

    public function __construct(Province $model)
    {
        $this->provinceModel = $model;
        $this->middleware(AdminAuth::class)->only(
            ["updateProvince",
            "deleteProvince",
            "updateDistrict",
            "deleteDistrict"
        ]);
    }

    public function index()
    {
        $provinces = Province::all();
        return view("admin/location/index",["provinces"=>$provinces]);
    }

    public function addProvince(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|unique:provinces"
        ]);
        $this->provinceModel->add($validated["name"]);
        return response()->back()->with("success","Thêm tỉnh/thành thành công");
    }

    public function editProvince($id)
    {
        $province = Province::first(where:array("id"=>$id));
        if(!$province){
            return;
        }
        return view("admin/location/edit_province",["province"=>$province]);
    }

    public function updateProvince(Request $request,$id)
    {
        $validated = $request->validate([
            "name" => "required|unique:locations,name,$id"
        ]);
        $province = Province::first(where:array("id"=>$id));
        if(!$province){
            return;
        }
        $province->update($validated["name"]);
        return response()->redirect("admin/location/province")
                        ->with("success","Cập nhật tỉnh/thành thành công");
    }

    public function deleteProvince($id)
    {
        $province = Province::first(where:array("id"=>$id));
        if(!$province){
            return;
        }
        $province->delete();
        return response()->redirect("admin/location/province")
                        ->with("success","Xóa tỉnh/thành thành công");
    }

    public function districts($id)
    {
        $province = Province::first(where:array("id"=>$id));
        if(!$province){
            return;
        }
        $districts = $province->getDistricts();
        return view("admin/location/province_districts",["province"=>$province,"districts"=>$districts]);
    }

    public function addDistrict(Request $request,$id)
    {
        $province = Province::first(where:array("id"=>$id));
        if(!$province){
            return;
        }
        $validated = $request->validate([
            "name" => "required|unique:province_districts"
        ]);
        $province->addDistrict($validated["name"]);
        return response()->back()->with("success","Thêm quận/huyện thành công");
    }

    public function editDistrict($province_id,$district_id)
    {
        $province = Province::first(where:array("id"=>$province_id));
        if(!$province){
            return;
        }
        $district = $province->getDistrict($district_id);
        return view("admin/location/edit_district",["province"=>$province,"district"=>$district]);
    }

    public function updateDistrict(Request $request,$province_id,$district_id)
    {
        $province = Province::first(where:array("id"=>$province_id));
        if(!$province){
            return;
        }
        $validated = $request->validate([
            "name" => "required|unique:province_districts,name,$district_id"
        ]);
        $province->updateDistrict($district_id,$validated["name"]);
        return response()->redirect("admin/location/province/{$province_id}/districts")
                        ->with("success","Cập nhật quận/huyện thành công");
    }

    public function deleteDistrict($province_id,$district_id)
    {
        $province = Province::first(where:array("id"=>$province_id));
        if(!$province){
            return;
        }
        $province->deleteDistrict($district_id);
        return response()->redirect("admin/location/province/{$province_id}/districts")
                        ->with("success","Xóa quận/huyện thành công");
    }
}