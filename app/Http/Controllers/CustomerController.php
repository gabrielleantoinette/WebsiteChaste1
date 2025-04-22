<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
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


    // PUBLIC FUNCTIONS
    public function viewProducts()
    {
        $products = Product::all();
        return view('produk', compact('products'));
    }

    public function detailProduct($id)
    {
        $product = Product::find($id);
        $variants = ProductVariant::where('product_id', $id)->get();
        return view('produk-detail', compact('product', 'variants'));
    }
}
