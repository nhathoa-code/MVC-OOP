<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;
use NhatHoa\Framework\Facades\DB;

class InvoiceAuth
{
    public function create(User $user)
    {
        return $user->can("read","invoice");
    }

    public function read(User $user)
    {
        return $user->can("update","invoice");
    }

    public function delete(User $user,$invoice_id)
    {
        $invoice = DB::table("sales")
                    ->where("id",$invoice_id)
                    ->where("employee_id",$user->id)
                    ->first();
        if($invoice && $user->can("delete","invoice")){
            return true;
        }
        return false;
    }
}