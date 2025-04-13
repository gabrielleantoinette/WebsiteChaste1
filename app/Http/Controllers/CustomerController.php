<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function view()
    {
        $customers = Customer::all();
        return view('admin.customers.view', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function createCustomerAction(Request $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = $request->password;
        $customer->phone = $request->phone;
        $customer->save();
        return redirect('/admin/customers');
    }

    public function detail($id)
    {
        $customer = Customer::find($id);
        return view('admin.customers.detail', compact('customer'));
    }

    public function updateCustomerAction(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = $request->password;
        $customer->phone = $request->phone;
        $customer->save();
        return redirect('/admin/customers/detail/' . $id);
    }
}
