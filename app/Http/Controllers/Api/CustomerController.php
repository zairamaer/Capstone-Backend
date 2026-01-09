<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class CustomerController extends Controller
{
        // For listing all customers
    public function index()
    {
        $customers = Customer::all();
        return CustomerResource::collection($customers);
    }
    // For showing a specific customer
    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return new CustomerResource($customer);
    }
    // For updating, we can return the updated resource
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('customers')->ignore($customer->customerID, 'customerID')
            ],
            'phone' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:6'
        ]);
        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        $customer->update($request->all());
        return new CustomerResource($customer);
    }
}