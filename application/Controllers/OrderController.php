<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Services\EmailService;
use NhatHoa\Framework\Event;
use NhatHoa\Framework\Facades\Gate;

class OrderController extends Controller
{
    protected $orderModel;

    public function __construct(Order $order)
    {
        $this->orderModel = $order;
    }

    public function index(Request $request)
    {
        if(!Gate::allows("read-order")){
            abort(401);
        }
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        $status = $request->query("status") ?? null;
        $keyword = $request->input("keyword"); 
        $number_map = array(
            "all" => $this->orderModel->count(),
            "pending" => $this->orderModel->count(where:array("status"=>"pending")),
            "toship" => $this->orderModel->count(where:array("status"=>"toship")),
            "shipping" => $this->orderModel->count(where:array("status"=>"shipping")),
            "completed" => $this->orderModel->count(where:array("status"=>"completed")),
            "cancelled" => $this->orderModel->count(where:array("status"=>"cancelled")),
        );
        $status_map = $this->orderModel->getMapStatus();
        list($orders, $total_orders) = $this->orderModel->getList($status ,$limit, $currentPage, $keyword);
        return view("admin/order/index",["orders" => $orders,"total_orders"=>$total_orders,"currentPage"=>$currentPage,"totalPages"=>ceil($total_orders / $limit),"number_map"=>$number_map,"status_map"=>$status_map]);
    }

    public function order($id)
    {
        $order = $this->orderModel->getOrder($id);
        if(!$order){
            return;
        }
        return view("admin/order/order",["order"=>$order]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-order")){
            abort(401);
        }
        $validated = $request->validate([
            "status" => "bail|required|in:pending,toship,shipping,completed,cancelled"
        ]);
        $status = $validated["status"];
        $order = $this->orderModel->first(where:array("id"=>$id));
        if($order){
           $order->updateOrder($status);
           if($status === "completed"){
                $emailService = new EmailService();
                Event::dispatch("order-email",[$order,$emailService,"VNH - Hoàn thành đơn hàng","complete_order"]);
           }
        }
        return response()->json("Trạng thái đơn hàng đã được chuyển sang \"{$order->getMapStatus()[$status]}\"",200);
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-order")){
            abort(401);
        }
        $order = Order::first(where:array("id"=>$id));
        if(!$order){
            return;
        }
        $order->deleteOrder();
        return response()->back();
    }
}