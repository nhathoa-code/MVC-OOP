<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\StoreRepositoryInterface;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\InvoiceService;
use NhatHoa\App\Validations\PosValidation;
use NhatHoa\Framework\Facades\DB;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\Gate;

class PosController extends Controller
{
    protected $storeRepository;
    protected $limit = 10;

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function invoices(Request $request)
    {
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        $query = DB::table("sales")
                    ->select(["sales.*","t.name as store","c.name as customer","c.id as customer_id"])
                    ->join("stores as t","t.id","=","sales.store_id")
                    ->join("customers as c","sales.customer_id","=","c.id")
                    ->orderBy("sales.created_at","desc");
        if($keyword){
            $query->where("sales.id",$keyword)->orWhere("c.name","like","%{$keyword}%")->orWhere("c.phone","like","%{$keyword}%");
        }
        $number_of_invoices = $query->count(false);
        $invoices = $query->limit($this->limit)->offset(($currentPage - 1) * $this->limit)->get();
        $totalPages = ceil($number_of_invoices / $this->limit);
        return view("admin/pos/invoice/index",["invoices"=>$invoices,"totalPages"=>$totalPages, "currentPage"=>$currentPage]);
    }

    public function showInvoice($invoice_id)
    {
        $invoice = DB::table("sales as s")->where("s.id",$invoice_id)
                                ->join("customers as c","c.id","=","s.customer_id")
                                ->join("users as u","u.id","=","s.employee_id")
                                ->join("stores as t","t.id","=","s.store_id")
                                ->join("provinces as p","p.id","=","t.province_id")
                                ->join("province_districts as pd","pd.id","=","t.district_id")
                                ->select(["s.*","t.name","t.address","c.name as customer_name","c.phone as customer_phone","p.name as province","pd.name as district","u.name as employee"])
                                ->first();
        if($invoice){
            $invoice->items = DB::table("sale_items as si")
                    ->where("sale_id",$invoice_id)
                    ->join("products as p","si.product_id","=","p.id")
                    ->leftJoin("product_colors as pc","si.color_id","=","pc.id")
                    ->select(["si.*","p.p_name as product_name","pc.color_name"])
                    ->get();
            return view("admin/pos/invoice/invoice",["sale"=>$invoice]);
        }
    }

    public function deleteInvoice($invoice_id)
    {
        if(!Gate::allows("delete-invoice",$invoice_id)) abort(401);
        DB::table("sales")->where("id",$invoice_id)->limit(1)->delete();
        return response()->back()->with("success","Xóa hóa đơn thành công");
    }

    public function createSaleView()
    {
        $stores = $this->storeRepository->getAll();
        return view("admin/pos/create_sale",["stores"=>$stores]);
    }

    public function createSale(Request $request, InventoryService $inventoryService, PosValidation $posValidation)
    {
        if(!Gate::allows("create-invoice")) abort(401);
        $validated = $posValidation->validateCreate($request);
        DB::beginTransaction();
        if(!isset($validated["customer"])){
            $validated["customer"] = DB::table("customers")->insert([
                "name" => $validated["customer_name"],
                "phone" => $validated["customer_phone"]
            ]);
        }
        $store = $this->storeRepository->getById($validated["store"]);
        $arr = $store->createSale($validated,$inventoryService);
        if($arr["status"] === false){
            DB::rollBack();
            return response()->json(array("message"=>$arr["message"]),400);
        }
        DB::commit();
        return response()->json($arr);
    }

    public function generateInvoice($id)
    {
        return InvoiceService::generate($id);
    }
    
    public function getProductsFromStore($store_id,ProductRepositoryInterface $productRepository)
    {
        $store = $this->storeRepository->getById($store_id);
        if(!$store){
            return response()->json("Cửa hàng không tồn tại!",400);
        }
        $products = array_map(function($item) use($store){
            $item->stock_in_store = $store->getProductStock($item->id);
            return $item;
        },$store->getProducts(null,null,null,true,$productRepository));
        return response()->json($products);
    }

    public function exportExcel(Request $request)
    {
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        $query = DB::table("sales")
                    ->select(["sales.*","t.name as store","c.name as customer","c.phone as phone"])
                    ->join("stores as t","t.id","=","sales.store_id")
                    ->join("customers as c","sales.customer_id","=","c.id")
                    ->orderBy("sales.created_at","desc");
        if($keyword){
            $query->where("sales.id",$keyword)->orWhere("c.name","like","%{$keyword}%")->orWhere("c.phone","like","%{$keyword}%");
        }
        $invoices = $query->limit($this->limit)->offset(($currentPage - 1) * $this->limit)->get();
        $data = array();
        foreach($invoices as $i){
            $arr = array($i->id,$i->created_at,$i->total_amount,$i->store,$i->customer,$i->phone);
            $data[] = $arr;
        }
        $headers = ["Mã hóa đơn", "Ngày tạo", "Tổng tiền" , "Cửa hàng", "Khách hàng", "Số điện thoại"];
        $autoSizeColumns = ["A","B","C","D","E","F"];
        $styleHeader = "A1:F1";
        return Excel::generate($data,"invoices",$headers,$autoSizeColumns,$styleHeader);
    }
}