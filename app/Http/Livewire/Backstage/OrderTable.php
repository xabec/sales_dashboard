<?php

namespace App\Http\Livewire\Backstage;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderTable extends TableComponent
{
    public $sortField = 'order_date';

    public $email;
    public $drawEmpty = false;
    public $example;

    protected $listeners = [
        'OrdersFilter' => 'OrdersFilter',
        'OrdersClear' => 'ordersClear'
    ];

    public function OrdersFilter($email)
    {
        $this->email = $email;
        $this->search = '';
        $this->hasSearch = false;
        $this->page = 1;

    }

    public function ordersClear()
    {
        $this->email = '';
        $this->hasSearch = true;
        $this->drawEmpty = true;
    }

    public function export()
    {
        $query = Order::search($this->search, $this->dateStart, $this->dateEnd, $this->email)
            ->rightJoin('items', 'items.order_id', 'orders.order_number')
            ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
            ->when($this->drawEmpty, function ($query) {
                $query->whereRaw(0);
            })
            ->groupBy('order_number');

        $orderIds = $query->pluck('order_number');

        if ($this->drawEmpty) {
            $this->drawEmpty = false;
        }

        $items = Item::select('items.*')
            ->whereIn('order_id', $orderIds->toArray())
            ->get();


        return new StreamedResponse(function () use ($query, $items) {
            // Open output stream
            $handle = fopen('php://output', 'wb');

            // Add CSV headers
            fputcsv($handle, [
                'Order number',
                'E-Mail address',
                'Order date',
                'Fulfillment status',
                'Payment status / Order status',
                'Payment date',
                'Fulfillment date',
                'Currency',
                'Subtotal',
                'Shipping_method',
                'Shipping_cost',
                'Tax method',
                'Taxes',
                'Total',
                'Coupon code',
                'Coupon code name',
                'Discount',
                'Billing name',
                'Billing country',
                'Billing address 1',
                'Billing address 2',
                'Billing city',
                'Billing state',
                'Billing zip',
                'Billing phone',
                'Shipping name',
                'Shipping country',
                'Shipping address 1',
                'Shipping address 2',
                'Shipping city',
                'Shipping state',
                'Shipping zip',
                'Shipping phone',
                'Gift cards',
                'Payment method',
                'Tracking number',
                'Special instructions'
            ]);

            $query->orderBy('id', 'asc')
                ->chunk(10000, function ($rows) use ($handle, $items) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->order_number,
                            $row->email,
                            $row->order_date,
                            $row->fullfilment_status,
                            $row->payment_status ?? $row->order_status,
                            $row->payment_date,
                            $row->fulfillment_date,
                            $row->currency,
                            $row->subtotal,
                            $row->shipping_method,
                            $row->shipping_cost,
                            $row->tax_method,
                            $row->taxes,
                            $row->total,
                            $row->coupon_code,
                            $row->coupon_code_name,
                            $row->discount,
                            $row->billing_name,
                            $row->billing_country,
                            $row->billing_address_street,
                            $row->billing_address_county,
                            $row->billing_city,
                            $row->billing_state,
                            $row->billing_zip,
                            $row->billing_number,
                            $row->shipping_name,
                            $row->shipping_country,
                            $row->shipping_address_street,
                            $row->shipping_address_county,
                            $row->shipping_city,
                            $row->shipping_state,
                            $row->shipping_zip,
                            $row->shipping_phone,
                            $row->gift_cards,
                            $row->payment_method,
                            $row->tracking_number,
                            $row->special_instructions
                        ]);
                    }
                });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename="crafty_orders.csv"',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }

    public function render()
    {
        $columns = [
            [
                'title' => 'Name',
                'attribute' => 'shipping_name',
                'sort' => true,
            ],
            [
                'title' => 'Order number',
                'attribute' => 'order_number',
                'sortField' => 'order_number',
                'sort' => true,
            ],
            [
                'title' => 'E-mail',
                'attribute' => 'email',
                'sortField' => 'email',
                'sort' => true,
            ],
            [
                'title' => 'Order Date',
                'attribute' => 'order_date',
                'sortField' => 'order_date',
                'sort' => true,
            ],
            [
                'title' => 'Fulfillment Date',
                'attribute' => 'fulfillment_date',
                'sortField' => 'fulfillment_date',
                'sort' => true,
            ],
            [
                'title' => 'Total',
                'attribute' => 'total',
                'sortField' => 'total',
                'sort' => true,
            ],
            [
                'title' => 'Discount',
                'attribute' => 'discount',
                'sortField' => 'discount',
                'sort' => true,
            ],
        ];

            $columns[] = [
                'title' => 'tools',
                'sort' => false,
                'tools' => ['item_details', 'order_details'],
            ];

        $ordersRows = Order::search($this->search, $this->dateStart, $this->dateEnd, $this->email)
                ->rightJoin('items', 'items.order_id', 'orders.order_number')
                ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
                ->when($this->drawEmpty, function ($query) {
                    $query->whereRaw(0);
                })
                ->groupBy('order_number')
                ->paginate($this->perPage);

        $orderIds = $ordersRows->pluck('order_number');

        if ($this->drawEmpty) {
            $this->drawEmpty = false;
        }


        return view('livewire.backstage.table', [
            'columns' => $columns,
            'resource' => 'orders',
            'rows' => $ordersRows,
            'items' => Item::select('items.*')
                ->whereIn('order_id', $orderIds->toArray())
                ->get()
        ]);
    }
}
