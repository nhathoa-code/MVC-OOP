<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\Category;
use NhatHoa\App\Repositories\Interfaces\RoleRepositoryInterface;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\OrderService;
use NhatHoa\App\Services\UserService;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Facades\DB;

class AdminController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(OrderService $orderService,Product $product,Category $category,InventoryService $inventoryService)
    {
        $data = array();
        $data['orders_statistics'] = DB::table("orders")
                        ->select(["HOUR(created_at) as hour","COUNT(*) as total_orders","SUM(total) as total"])
                        ->whereDate("created_at",date('Y-m-d'))
                        ->groupBy("HOUR(created_at)")
                        ->get();
        $data["total_sales"] = DB::table("orders")
                                ->select(["SUM(total) as total_sales"])
                                ->where("status","!=","cancelled")
                                ->getValue("total_sales");
        $data["orders"] = array(
            "all" => $orderService->countOrders(),
            "pending" => $orderService->countOrders("pending"),
            "toship" => $orderService->countOrders("toship"),
            "shipping" => $orderService->countOrders("shipping"),
            "completed" => $orderService->countOrders("completed"),
            "cancelled" => $orderService->countOrders("cancelled"),
        );
        $data["total_products"] = $product->count();
        $data["total_categories"] = $category->count();
        $data["top_10_saled_products"] = $orderService->getTopSaled(20);
        $threshold = 5;
        $data["out_of_stock_products"] = $inventoryService->getOutOfStock($threshold);
        return view("admin/index",$data);
    }


    public function loginView()
    {
        if(Auth::check("admin")){
            return redirect("admin");
        }
        return view("admin/login");
    }

    public function login(Request $request,RoleRepositoryInterface $roleRepository)
    {  
        $validated = $request->validate([
            "login_key" => "required",
            "password" => "required"
        ]);
        $admin = $this->userService->adminLogin($validated["login_key"],$validated["password"],$roleRepository);
        if($admin){
            Auth::login($admin,"admin");
            return redirect("admin");
        }else{
            return response()->back()->with("error","Username hoặc mật khẩu không đúng");
        }
    }

    public function logout()
    {
        Auth::logout("admin");
        return redirect("admin/login");
    }

    public function statistical(Request $request,$type)
    {
        switch($type){
            case "date":
                $data = DB::table("orders")
                        ->select(["HOUR(created_at) as hour","COUNT(*) as total_orders","SUM(total) as total"])
                        ->where("status","!=","cancelled")
                        ->whereDate("created_at",$request->input("date"))
                        ->groupBy("HOUR(created_at)")
                        ->get();
                return response()->json(["data"=> $data],200);
                break;
            case "week":
                $data = DB::table("orders")
                        ->select(["DATE(created_at) as date","COUNT(*) as total_orders","SUM(total) as total"])
                        ->where("status","!=","cancelled")
                        ->whereDate("created_at",">=",$request->input("start_date"))
                        ->whereDate("created_at","<=",$request->input("end_date"))
                        ->groupBy("DATE(created_at)")
                        ->get();
                return response()->json(["data"=> $data],200);
                break;
            case "month":
                $data = DB::table("orders")
                        ->select(["DATE(created_at) as date","COUNT(*) as total_orders","SUM(total) as total"])
                        ->where("status","!=","cancelled")
                        ->whereMonth("created_at",$request->input("month"))
                        ->whereYear("created_at",$request->input("year"))
                        ->groupBy("DATE(created_at)")
                        ->get();
                return response()->json(["data"=> $data],200);
                break;
            case "year":
                $data = DB::table("orders")
                        ->select(["MONTH(created_at) as month","COUNT(*) as total_orders","SUM(total) as total"])
                        ->where("status","!=","cancelled")
                        ->whereYear("created_at",$request->input("year"))
                        ->groupBy("MONTH(created_at)")
                        ->get();
                return response()->json(["data"=> $data],200);
                break;
            default:
                return null; 
        }

    }

    public function notFound()
    {
        return view("admin/not_found");
    }
}