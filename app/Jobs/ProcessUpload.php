<?php

namespace App\Jobs;

use App\Models\Import;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SplFileObject;

class ProcessUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Import $import;
    private $count;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = new SplFileObject(storage_path('app/' . $this->import->filepath), 'rb');
        $file->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::READ_AHEAD
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::DROP_NEW_LINE
        );
        $headers = $file->fgetcsv();
        $headers = str_replace(' ','_',$headers);
        $this->count = count($headers);

        $failMessages = [];

        if($this->count == 43)
        {
            $failures = 0;
            $success = 0;
            $duplicate = 0;

            while ($row = $file->fgetcsv()) {
                $data = array_combine($headers, $row);
                $data['Order_Date_and_Time_Stamp'] = Carbon::parse($data['Order_Date_and_Time_Stamp']);
                $data['Payment_Date_and_Time_Stamp'] = Carbon::parse($data['Payment_Date_and_Time_Stamp']);
                $data['Fulfillment_Date_and_Time_Stamp'] = Carbon::parse($data['Fulfillment_Date_and_Time_Stamp']);
                $data['Subtotal'] = Str::substr($data['Subtotal'], 1, 10);
                $data['Shipping_Cost'] = Str::substr($data['Shipping_Cost'], 1, 10);
                $data['Taxes'] = Str::substr($data['Taxes'], 1, 10);
                $data['Total'] = Str::substr($data['Total'], 1, 10);
                $data['Discount'] = Str::substr($data['Discount'], 2, 10);
                $data['LineItem_Sale_Price'] = Str::substr($data['LineItem_Sale_Price'], 1, 10);
                $order = Order::where('order_number', $data['﻿Order_#'])->first();
                $realDuplicate = false;


                if ($order) {
                    $realDuplicate = $order->import_id !== $this->import->id;

                    if ($realDuplicate) {
                        $duplicate++;
                        continue;
                    }
                }

                $validator = Validator::make($data, [
                    'Order_#' => 'unique:orders|max:255',
                    'Email_Address' => 'required|max:255',
                    'Order_Date_and_Time_Stamp' => 'required',
                    'Order_Status' => 'nullable|max:255',
                    'Fulfillment_Status' => 'nullable|max:255',
                    'Payment_Status' => 'nullable|max:255',
                    'Payment_Date_and_Time_Stamp' => 'nullable',
                    'Fulfillment_Date_and_Time_Stamp' => 'nullable',
                    'Currency' => 'required|max:255',
                    'Subtotal' => 'required',
                    'Shipping_Method' => 'required|max:255',
                    'Shipping_Cost' => 'required',
                    'Tax_Method' => 'nullable|max:255',
                    'Taxes' => 'required',
                    'Total' => 'required',
                    'Coupon_Code' => 'nullable|max:255',
                    'Coupon_Code_Name' => 'nullable|max:255',
                    'Discount' => 'nullable',
                    'Billing_Name' => 'nullable|max:255',
                    'Billing_Country' => 'nullable|max:255',
                    'Billing_Street_Address' => 'nullable|max:255',
                    'Billing_Street_Address 2' => 'nullable|max:255',
                    'Billing_City' => 'nullable|max:255',
                    'Billing_State' => 'nullable|max:255',
                    'Billing_Zip' => 'nullable|max:255',
                    'Billing_Phone' => 'nullable',
                    'Shipping_Name' => 'required|max:255',
                    'Shipping_Country' => 'required|max:255',
                    'Shipping_Street_Address' => 'nullable|max:255',
                    'Shipping_Street_Address 2' => 'nullable|max:255',
                    'Shipping_City' => 'nullable|max:255',
                    'Shipping_State' => 'nullable|max:255',
                    'Shipping_Zip' => 'nullable|max:255',
                    'Shipping_Phone' => 'required',
                    'Gift_Cards' => 'nullable|max:255',
                    'Payment_Method' => 'required|max:255',
                    'Tracking_#' => 'nullable|max:255',
                    'Special_Instructions' => 'nullable',
                    'LineItem_Name' => 'required|max:255',
                    'LineItem_SKU' => 'required:max:255',
                    'LineItem_Options' => 'nullable|max:255',
                    'LineItem_Add-ons' => 'nullable',
                    'LineItem_Qty' => 'required',
                    'LineItem_Sale_Price' => 'required',
                    'LineItem_Type' => 'required|max:255'
                ]);

                if ($validator->fails()) {
                    $failures++;
                    $failMessages[] = $validator->getMessageBag()->toArray();
                    continue;
                } else {
                    if (! $order) {
                        $order = new Order();
                        $order->order_number = $data['﻿Order_#'];
                        $order->import_id = $this->import->id;
                        $order->email = $data['Email_Address'];
                        $order->order_date = $data['Order_Date_and_Time_Stamp'];
                        $order->order_status = $data['Order_Status'];
                        $order->payment_date = $data['Payment_Date_and_Time_Stamp'];
                        $order->fulfillment_date = $data['Fulfillment_Date_and_Time_Stamp'];
                        $order->currency = $data['Currency'];
                        $order->subtotal = $data['Subtotal'];
                        $order->shipping_method = $data['Shipping_Method'];
                        $order->shipping_cost = $data['Shipping_Cost'];
                        $order->tax_method = $data['Tax_Method'];
                        $order->taxes = $data['Taxes'];
                        $order->total = $data['Total'];
                        $order->coupon_code = $data['Coupon_Code'];
                        $order->coupon_code_name = $data['Coupon_Code_Name'];
                        $order->discount = $data['Discount'] ?? 0;
                        $order->billing_name = $data['Billing_Name'];
                        $order->billing_country = $data['Billing_Country'];
                        $order->billing_address_street = $data['Billing_Street_Address'];
                        $order->billing_address_county = $data['Billing_Street_Address_2'];
                        $order->billing_city = $data['Billing_City'];
                        $order->billing_state = $data['Billing_State'];
                        $order->billing_zip = $data['Billing_Zip'];
                        $order->billing_number = $data['Billing_Phone'];
                        $order->shipping_name = $data['Shipping_Name'];
                        $order->shipping_country = $data['Shipping_Country'];
                        $order->shipping_address_street = $data['Shipping_Street_Address'];
                        $order->shipping_address_county = $data['Shipping_Street_Address_2'];
                        $order->shipping_city = $data['Shipping_City'];
                        $order->shipping_state = $data['Shipping_State'];
                        $order->shipping_zip = $data['Shipping_Zip'];
                        $order->shipping_phone = $data['Shipping_Phone'];
                        $order->gift_cards = $data['Gift_Cards'];
                        $order->payment_method = $data['Payment_Method'];
                        $order->tracking_number = $data['Tracking_#'];
                        $order->special_instructions = $data['Special_Instructions'];
                        $order->save();
                    }
                }

                $item = new Item();
                $item->order_id = $order->order_number;
                $item->customer_email = $order->email;
                $item->lineitem_name = $data['LineItem_Name'];
                $item->lineitem_sku = $data['LineItem_SKU'];
                $item->lineitem_options = $data['LineItem_Options'];
                $item->lineitem_addons = $data['LineItem_Add-ons'];
                $item->lineitem_qty = $data['LineItem_Qty'];
                $item->lineitem_price = $data['LineItem_Sale_Price'];
                $item->lineitem_type = $data['LineItem_Type'];
                $item->save();

                $success++;
            } // end of while

            $this->import->success = $success;
            $this->import->failures = $failures;
            $this->import->duplicate = $duplicate;

            $this->import->complete = true;
            $this->import->save();

        }
        else
        {

            $failures = 0;
            $success = 0;
            $duplicate = 0;

            while ($row = $file->fgetcsv()) {
                $data = array_combine($headers, $row);
                $data['Order_Date_and_Time_Stamp'] = Carbon::parse($data['Order_Date_and_Time_Stamp']);
                $data['Payment_Date_and_Time_Stamp'] = Carbon::parse($data['Payment_Date_and_Time_Stamp']);
                $data['Fulfillment_Date_and_Time_Stamp'] = Carbon::parse($data['Fulfillment_Date_and_Time_Stamp']);
                $data['Subtotal'] = Str::substr($data['Subtotal'], 1, 10);
                $data['Shipping_Cost'] = Str::substr($data['Shipping_Cost'], 1, 10);
                $data['Taxes'] = Str::substr($data['Taxes'], 1, 10);
                $data['Total'] = Str::substr($data['Total'], 1, 10);
                $data['Discount'] = Str::substr($data['Discount'], 2, 10);
                $data['LineItem_Sale_Price'] = Str::substr($data['LineItem_Sale_Price'], 1, 10);
                $order = Order::where('order_number', $data['﻿Order_#'])->first();
                $realDuplicate = false;


                if ($order) {
                    $realDuplicate = $order->import_id !== $this->import->id;

                    if ($realDuplicate) {
                        $duplicate++;
                        continue;
                    }
                }

                $validator = Validator::make($data, [
                    'Order_#' => 'unique:orders|max:255',
                    'Email_Address' => 'required|max:255',
                    'Order_Date_and_Time_Stamp' => 'required',
                    'Order_Status' => 'nullable|max:255',
                    'Fulfillment_Status' => 'nullable|max:255',
                    'Payment_Status' => 'nullable|max:255',
                    'Payment_Date_and_Time_Stamp' => 'nullable',
                    'Fulfillment_Date_and_Time_Stamp' => 'nullable',
                    'Currency' => 'required|max:255',
                    'Subtotal' => 'required',
                    'Shipping_Method' => 'required|max:255',
                    'Shipping_Cost' => 'required',
                    'Tax_Method' => 'nullable|max:255',
                    'Taxes' => 'required',
                    'Total' => 'required',
                    'Coupon_Code' => 'nullable|max:255',
                    'Coupon_Code_Name' => 'nullable|max:255',
                    'Discount' => 'nullable',
                    'Billing_Name' => 'nullable|max:255',
                    'Billing_Country' => 'nullable|max:255',
                    'Billing_Street_Address' => 'nullable|max:255',
                    'Billing_Street_Address 2' => 'nullable|max:255',
                    'Billing_City' => 'nullable|max:255',
                    'Billing_State' => 'nullable|max:255',
                    'Billing_Zip' => 'nullable|max:255',
                    'Billing_Phone' => 'nullable',
                    'Shipping_Name' => 'required|max:255',
                    'Shipping_Country' => 'required|max:255',
                    'Shipping_Street_Address' => 'nullable|max:255',
                    'Shipping_Street_Address 2' => 'nullable|max:255',
                    'Shipping_City' => 'nullable|max:255',
                    'Shipping_State' => 'nullable|max:255',
                    'Shipping_Zip' => 'nullable|max:255',
                    'Shipping_Phone' => 'required',
                    'Gift_Cards' => 'nullable|max:255',
                    'Payment_Method' => 'required|max:255',
                    'Tracking_#' => 'nullable|max:255',
                    'Special_Instructions' => 'nullable',
                    'LineItem_Name' => 'required|max:255',
                    'LineItem_SKU' => 'required:max:255',
                    'LineItem_Options' => 'nullable|max:255',
                    'LineItem_Add-ons' => 'nullable',
                    'LineItem_Qty' => 'required',
                    'LineItem_Sale_Price' => 'required',
                    'LineItem_Type' => 'required|max:255'
                ]);

                if ($validator->fails()) {
                    $failures++;
                    $failMessages[] = $validator->getMessageBag()->toArray();
                    continue;
                } else {
                    if (! $order) {
                        $order = new Order();
                        $order->order_number = $data['﻿Order_#'];
                        $order->import_id = $this->import->id;
                        $order->email = $data['Email_Address'];
                        $order->order_date = $data['Order_Date_and_Time_Stamp'];
                        $order->fullfilment_status = $data['Fulfillment_Status'];
                        $order->payment_status = $data['Payment_Status'];
                        $order->payment_date = $data['Payment_Date_and_Time_Stamp'];
                        $order->fulfillment_date = $data['Fulfillment_Date_and_Time_Stamp'];
                        $order->currency = $data['Currency'];
                        $order->subtotal = $data['Subtotal'];
                        $order->shipping_method = $data['Shipping_Method'];
                        $order->shipping_cost = $data['Shipping_Cost'];
                        $order->tax_method = $data['Tax_Method'];
                        $order->taxes = $data['Taxes'];
                        $order->total = $data['Total'];
                        $order->coupon_code = $data['Coupon_Code'];
                        $order->coupon_code_name = $data['Coupon_Code_Name'];
                        $order->discount = $data['Discount'] ?? 0;
                        $order->billing_name = $data['Billing_Name'];
                        $order->billing_country = $data['Billing_Country'];
                        $order->billing_address_street = $data['Billing_Street_Address'];
                        $order->billing_address_county = $data['Billing_Street_Address_2'];
                        $order->billing_city = $data['Billing_City'];
                        $order->billing_state = $data['Billing_State'];
                        $order->billing_zip = $data['Billing_Zip'];
                        $order->billing_number = $data['Billing_Phone'];
                        $order->shipping_name = $data['Shipping_Name'];
                        $order->shipping_country = $data['Shipping_Country'];
                        $order->shipping_address_street = $data['Shipping_Street_Address'];
                        $order->shipping_address_county = $data['Shipping_Street_Address_2'];
                        $order->shipping_city = $data['Shipping_City'];
                        $order->shipping_state = $data['Shipping_State'];
                        $order->shipping_zip = $data['Shipping_Zip'];
                        $order->shipping_phone = $data['Shipping_Phone'];
                        $order->gift_cards = $data['Gift_Cards'];
                        $order->payment_method = $data['Payment_Method'];
                        $order->tracking_number = $data['Tracking_#'];
                        $order->special_instructions = $data['Special_Instructions'];
                        $order->save();
                    }
                }

                $item = new Item();
                $item->order_id = $order->order_number;
                $item->customer_email = $order->email;
                $item->lineitem_name = $data['LineItem_Name'];
                $item->lineitem_sku = $data['LineItem_SKU'];
                $item->lineitem_options = $data['LineItem_Options'];
                $item->lineitem_addons = $data['LineItem_Add-ons'];
                $item->lineitem_qty = $data['LineItem_Qty'];
                $item->lineitem_price = $data['LineItem_Sale_Price'];
                $item->lineitem_type = $data['LineItem_Type'];
                $item->save();

                $success++;
            } // end of while

            $this->import->success = $success;
            $this->import->failures = $failures;
            $this->import->duplicate = $duplicate;
            $this->import->complete = true;
            $this->import->failures_list = $failMessages;

            $this->import->save();

        }
    }
}
