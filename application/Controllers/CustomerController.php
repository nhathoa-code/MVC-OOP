<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Repositories\Interfaces\CustomerRepositoryInterface;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\DB;
use NhatHoa\Framework\Facades\Gate;

class CustomerController extends Controller
{
    protected $customerRepository;
    protected $limit = 10;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request)
    {
        if(!Gate::allows("read-customer")) abort(401);
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword");
        list($customers,$number_of_customers) = $this->customerRepository->getAll($currentPage, $this->limit, $keyword);
        $totalPages = ceil($number_of_customers / $this->limit);
        return view("admin/customer/index",["customers"=>$customers,"totalPages" => $totalPages, "currentPage" => $currentPage]);
    }

    public function addView()
    {
        return view("admin/customer/add");
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-customer")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|min:3",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/|unique:customers"
        ]);
        $this->customerRepository->create($validated);
        return response()->redirect("admin/customer")->with("success","Thêm khách hàng thành công");
    }

    public function edit($id)
    {
        $customer = $this->customerRepository->getById($id);
        if($customer){
            return view("admin/customer/edit",["customer"=>$customer]);
        }
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-customer")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|min:3",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/|unique:customers,phone,$id"
        ]);
        $customer = $this->customerRepository->getById($id);
        if($customer){
            $this->customerRepository->update($customer,$validated);
            return response()->back()->with("success","Cập nhật khách hàng thành công");
        }
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-customer")) abort(401);
        $customer = $this->customerRepository->getById($id);
        if($customer){
            $this->customerRepository->delete($customer);
            return response()->back()->with("success","Xóa khách hàng thành công");
        }
    }

    public function fetchCustomer(Request $request)
    {
        $validated = $request->validate([
            "phone" => "bail|required|regex:/^0[0-9]{9}$/"
        ]);
        $customer = $this->customerRepository->getByPhoneNumber($validated["phone"]);
        if(!$customer){
            return response()->json(["message"=>"customer not found"],404);
        }else{  
            return response()->json(["customer"=>$customer]);
        }
    }

    public function purchaseHistory(Request $request,$id)
    {
        $customer = $this->customerRepository->getById($id);
        if(!$customer) return "Khách hàng không tồn tại";
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        $query = DB::table("sales")
                    ->where("sales.customer_id",$customer->id)
                    ->select(["sales.*","t.name as store","c.name as customer"])
                    ->join("stores as t","t.id","=","sales.store_id")
                    ->join("customers as c","sales.customer_id","=","c.id")
                    ->orderBy("sales.created_at","desc");
        $number_of_invoices = $query->count(false);
        $invoices = $query->limit($this->limit)->offset(($currentPage - 1) * $this->limit)->get();
        $totalPages = ceil($number_of_invoices / $this->limit);
        return view("admin/customer/purchase_history",["customer"=>$customer,"invoices"=>$invoices,"totalPages"=>$totalPages, "currentPage"=>$currentPage]);
    }

    public function exportExcel(Request $request)
    {
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword");
        list($customers) = $this->customerRepository->getAll($currentPage, $this->limit, $keyword);
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