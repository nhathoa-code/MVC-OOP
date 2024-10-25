<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Repositories\Interfaces\OrderRepositoryInterface;
use NhatHoa\App\Services\EmailService;
use NhatHoa\App\Services\OrderService;
use NhatHoa\App\Services\UserService;
use NhatHoa\Framework\Event;
use NhatHoa\Framework\Facades\Gate;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request,OrderService $orderService)
    {
        if(!Gate::allows("read-order")) abort(401);
        $currentPage = max((int) $request->query("page"),1);
        $status = $request->query("status");
        $keyword = $request->query("keyword"); 
        $number_map = array(
            "all" => Order::count(),
            "pending" => Order::count(where:array("status"=>"pending")),
            "toship" => Order::count(where:array("status"=>"toship")),
            "shipping" => Order::count(where:array("status"=>"shipping")),
            "completed" => Order::count(where:array("status"=>"completed")),
            "cancelled" => Order::count(where:array("status"=>"cancelled")),
        );
        $status_map = $orderService->getMapStatus();
        list($orders, $total_orders) = $this->orderRepository->getAll($status,10,$currentPage,$keyword);
        return view("admin/order/index",["orders" => $orders,"total_orders"=>$total_orders,"currentPage"=>$currentPage,"totalPages"=>ceil($total_orders / 10),"number_map"=>$number_map,"status_map"=>$status_map]);
    }

    public function order($id)
    {
        $order = $this->orderRepository->getById($id);
        if(!$order) return;
        return view("admin/order/order",["order"=>$order]);
    }

    public function update(Request $request,$id,UserService $userService,OrderService $orderService)
    {
        if(!Gate::allows("update-order")) abort(401);
        $validated = $request->validate([
            "status" => "bail|required|in:pending,toship,shipping,completed,cancelled"
        ]);
        $status = $validated["status"];
        $order = Order::first(where:array("id"=>$id));
        if($order){
           $this->orderRepository->update($order,$status,$userService);
           if($status === "completed"){
                $emailService = new EmailService();
                Event::dispatch("order-email",[$order,$emailService,"VNH - Hoàn thành đơn hàng","complete_order"]);
           }
        }
        return response()->json("Trạng thái đơn hàng đã được chuyển sang \"{$orderService->getMapStatus()[$status]}\"",200);
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-order")) abort(401);
        $order = Order::first(where:array("id"=>$id));
        if(!$order) return;
        $this->orderRepository->delete($order);
        return response()->back();
    }
}