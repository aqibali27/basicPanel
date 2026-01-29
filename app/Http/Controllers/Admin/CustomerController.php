<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function __construct(
        private User             $customer,
        private BusinessSetting  $business_setting
    )
    {
    }


    /**
     * @param Request $request
     * @return Renderable
     */
    public function customer_list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = $this->customer->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = $this->customer;
        }

        $customers = $customers->where('user_type', null)->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.list', compact('customers', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $customers = $this->customer->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();

        return response()->json([
            'view' => view('admin-views.customer.partials._table', compact('customers'))->render(),
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Renderable
     */
    public function view($id, Request $request): RedirectResponse|Renderable
    {
        $search = $request->search;
        $customer = $this->customer->find($id);

        if (!isset($customer)) {
            Toastr::error(translate('Customer not found!'));
            return back();
        }

        return view('admin-views.customer.customer-view', compact('customer', 'search'));
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function transaction(Request $request): Renderable
    {
        $query_param = ['search' => $request['search']];
        $search = $request['search'];

        $transition = $this->point_transitions->with(['customer'])->latest()
            ->when($request->has('search'), function ($q) use ($search) {
                $q->whereHas('customer', function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%");
                    }
                });
            })
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.customer.transaction-table', compact('transition', 'search'));
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function customer_transaction($id, Request $request): Renderable
    {
        $search = $request['search'];
        $query_param = ['search' => $search];

        $transition = $this->point_transitions->with(['customer'])
            ->where(['user_id' => $id])
            ->when($request->has('search'), function ($query) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $query->where('transaction_id', 'like', "%{$value}%");
                }
            })
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.customer.transaction-table', compact('transition', 'search'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update_status(Request $request, $id): JsonResponse
    {
        $this->customer->findOrFail($id)->update(['is_active' => $request['status']]);
        return response()->json($request['status']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $this->customer->findOrFail($request['id'])->delete();
            Toastr::success(translate('user_deleted_successfully!'));

        } catch (\Exception $e) {
            Toastr::error(translate('user_not_found!'));
        }
        return back();
    }

    /**
     * @return StreamedResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function excel_import(): StreamedResponse|string
    {
        $users = $this->customer->select('f_name as First Name', 'l_name as Last Name', 'email as Email', 'is_active as Active', 'phone as Phone', 'point as Point')->get();
        return (new FastExcel($users))->download('customers.xlsx');
    }
}
