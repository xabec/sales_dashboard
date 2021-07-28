<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUpload;
use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SplFileObject;

class UploadController extends Controller
{
    public $count;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('upload');
    }

    private function validateHeaders($headers)
    {
        $this->count = count($headers);
        if ($this->count == 43)
        {
            $validHeaders = [
                '﻿Order #', 'Email Address', 'Order Date and Time Stamp', 'Order Status', 'Payment Date and Time Stamp', 'Fulfillment Date and Time Stamp', 'Currency', 'Subtotal', 'Shipping Method', 'Shipping Cost', 'Tax Method', 'Taxes', 'Total', 'Coupon Code', 'Coupon Code Name', 'Discount', 'Billing Name', 'Billing Country', 'Billing Street Address', 'Billing Street Address 2', 'Billing City', 'Billing State', 'Billing Zip', 'Billing Phone', 'Shipping Name', 'Shipping Country', 'Shipping Street Address', 'Shipping Street Address 2', 'Shipping City', 'Shipping State', 'Shipping Zip', 'Shipping Phone', 'Gift Cards', 'Payment Method', 'Tracking #', 'Special Instructions', 'LineItem Name', 'LineItem SKU', 'LineItem Options', 'LineItem Add-ons', 'LineItem Qty', 'LineItem Sale Price', 'LineItem Type'
            ];

            $diff = array_diff($headers, $validHeaders);
            return empty($diff);
        }
        else if ($this->count == 44)
        {
            $validHeadersNew = [
                '﻿Order #', 'Email Address', 'Order Date and Time Stamp', 'Fulfillment Status', 'Payment Status', 'Payment Date and Time Stamp', 'Fulfillment Date and Time Stamp', 'Currency', 'Subtotal', 'Shipping Method', 'Shipping Cost', 'Tax Method', 'Taxes', 'Total', 'Coupon Code', 'Coupon Code Name', 'Discount', 'Billing Name', 'Billing Country', 'Billing Street Address', 'Billing Street Address 2', 'Billing City', 'Billing State', 'Billing Zip', 'Billing Phone', 'Shipping Name', 'Shipping Country', 'Shipping Street Address', 'Shipping Street Address 2', 'Shipping City', 'Shipping State', 'Shipping Zip', 'Shipping Phone', 'Gift Cards', 'Payment Method', 'Tracking #', 'Special Instructions', 'LineItem Name', 'LineItem SKU', 'LineItem Options', 'LineItem Add-ons', 'LineItem Qty', 'LineItem Sale Price', 'LineItem Type'
            ];

            $diff = array_diff($headers, $validHeadersNew);
            return empty($diff);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, ['orders' => 'required|mimes:csv,txt']);

        $ordersFile = $request->file('orders');

        $file = new SplFileObject($ordersFile->getPathname(), 'rb');
        $file->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::READ_AHEAD
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::DROP_NEW_LINE
        );
        $headers = $file->fgetcsv();

        if (! $this->validateHeaders($headers)) {
            return Redirect::to('/upload')->with('error', 'Invalid file headers');
        }

        $filename = $ordersFile->getClientOriginalName();

        $tmpName = Str::random(40).'.'.$ordersFile->getClientOriginalExtension();

        if (! Storage::exists('uploads')) {
            Storage::makeDirectory('uploads');
        }

        $filePath = $ordersFile->storeAs('uploads', $tmpName);

        $import = Import::create([
           'filename' => $filename,
           'filepath' => $filePath
        ]);

        ProcessUpload::dispatch($import); // process file in background

        return Redirect::to('/upload')->with('success', "File uploaded successfully. Processing...");
    }
}
