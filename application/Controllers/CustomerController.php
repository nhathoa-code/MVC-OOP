<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Customer;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\DB;

class CustomerController extends Controller
{
    protected $customerModel;

    public function __construct(Customer $customerModel)
    {
        $this->customerModel = $customerModel;
    }

    public function index(Request $request)
    {
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        list($customers,$number_of_customers) = $this->customerModel->getList($currentPage, $limit, $keyword);
        $totalPages = ceil($number_of_customers / $limit);
        return view("admin/customer/index",["customers"=>$customers,"totalPages" => $totalPages, "currentPage" => $currentPage]);
    }

    public function addView()
    {
        return view("admin/customer/add");
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            "name" => "bail|required|string|min:3",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/|unique:customers"
        ]);
        $this->customerModel->saveCustomer($validated);
        return response()->redirect("admin/customer")->with("success","Thêm khách hàng thành công");
    }

    public function edit($id)
    {
        $customer = $this->customerModel->first(where:array("id"=>$id));
        if($customer){
            return view("admin/customer/edit",["customer"=>$customer]);
        }
    }

    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            "name" => "bail|required|string|min:3",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/|unique:customers,phone,$id"
        ]);
        $customer = $this->customerModel->first(where:array("id"=>$id));
        if($customer){
            $customer->updateCustomer($validated);
            return response()->back()->with("success","Cập nhật khách hàng thành công");
        }
    }

    public function delete($id)
    {
        $customer = $this->customerModel->first(where:array("id"=>$id));
        if($customer){
            $customer->deleteCustomer();
            return response()->back()->with("success","Xóa khách hàng thành công");
        }
    }

    public function fetchCustomer(Request $request)
    {
        $validated = $request->validate([
            "phone" => "bail|required|regex:/^0[0-9]{9}$/"
        ]);
        $customer = $this->customerModel->first(where:array("phone"=>$validated["phone"]));
        if(!$customer){
            return response()->json(["message"=>"customer not found"],404);
        }else{  
            return response()->json(["customer"=>$customer]);
        }
    }

    public function purchaseHistory(Request $request,$id)
    {
        $customer = $this->customerModel->first(where:array("id"=>$id));
        if(!$customer){
            return "Khách hàng không tồn tại";
        }
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        $query = DB::table("sales")
                    ->where("sales.customer_id",$customer->id)
                    ->select(["sales.*","t.name as store","c.name as customer"])
                    ->join("stores as t","t.id","=","sales.store_id")
                    ->join("customers as c","sales.customer_id","=","c.id")
                    ->orderBy("sales.created_at","desc");
        $number_of_invoices = $query->count(false);
        $invoices = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        $totalPages = ceil($number_of_invoices / $limit);
        return view("admin/customer/purchase_history",["customer"=>$customer,"invoices"=>$invoices,"totalPages"=>$totalPages, "currentPage"=>$currentPage]);
    }

    public function exportExcel(Request $request)
    {
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        list($customers) = $this->customerModel->getList($currentPage, $limit, $keyword);
        $data = array();
        foreach($customers as $c){
            $arr = array($c->name,$c->phone);
            $data[] = $arr;
        }
        $headers = ["Tên khách hàng", "Số điện thoại"];
        $autoSizeColumns = ["A","B"];
        $styleHeader = "A1:B1";
        return Excel::generate($data,"customers",$headers,$autoSizeColumns,$styleHeader);
    }
}