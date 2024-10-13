<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;

class UserValidation
{
    public function validateCreate(Request $request)
    {
        return $request->validate([
            "name" => "required",
            "login_key" => "bail|required|unique:users",
            "email" => "bail|required|email|unique:users",
            "password" => "bail|required|regex:/^[^\s]{6,}$/",
            "role" => "bail|nullable|required|exists:roles,id"
        ]);
    }

    public function validateUpdate(Request $request, $id)
    {
        return $request->validate([
            "name" => "required",
            "login_key" => "bail|required|unique:users,login_key,$id",
            "email" => "bail|required|email|unique:users,email,$id",
            "password" => "bail|nullable|required|regex:/^[^\s]{6,}$/",
            "role" => "bail|nullable|required|exists:roles,id"
        ]);
    }

    public function validateCreateAddress(Request $request)
    {
        return $request->validate([
            "name" => "required",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "province_id" => "bail|required|numeric",
            "district" => "required",
            "district_id" => "bail|required|numeric",
            "ward" => "required",
            "ward_code" => "bail|required|numeric",
            "address" => "required"
        ]);
    }

    public function validateUpdateAddress(Request $request)
    {
        return $this->validateCreateAddress($request);
    }

    public function validateUpdateProfile(Request $request)
    {
        return $request->validate([
            "name" => "required",
            "birth_day" => "bail|required|regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "province_id" => "bail|required|numeric",
            "district" => "required",
            "district_id" => "bail|required|numeric",
            "ward" => "required",
            "ward_code" => "bail|required|numeric",
            "gender" => "bail|required|in:boy,girl"
        ]);
    }

    public function validateUpdatePassword(Request $request)
    {
        return $request->validate([
            "oldpass" => "required",
            "newpass" => "bail|required|regex:/^[^\s]{6,}$/",
            "retype_newpass" => "bail|required|same:newpass"
        ]);
    }
}