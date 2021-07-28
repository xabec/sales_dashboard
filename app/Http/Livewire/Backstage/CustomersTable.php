<?php

namespace App\Http\Livewire\Backstage;

use App\Models\Item;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomersTable extends TableComponent
{
    public $sortField = 'total_orders_customers';

    public function export()
    {
        $query = Order::searchCustomer($this->search, $this->dateStart, $this->dateEnd)
            ->selectRaw( 'SUM(`total`)  AS total_price_customers' )
            ->selectRaw( 'COUNT(*)  AS total_orders_customers')
            ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
            ->groupBy('email');


        $items = Item::query()->selectRaw('SUM(`lineitem_qty`) as `real_qty`, `lineitem_name`, LOWER(`customer_email`) as `email`')
            ->groupBy('customer_email', 'lineitem_sku')
            ->get();

        $topItems = $items->groupBy('email')
            ->map(fn ($items) => $items->sortByDesc('real_qty')->first())
            ->toArray();

        return new StreamedResponse(function () use ($query, $topItems) {
            // Open output stream
            $handle = fopen('php://output', 'wb');

            // Add CSV headers
            fputcsv($handle, [
                'Name',
                'E-Mail address',
                'Total orders',
                'Money spent',
                'Favorite item',
                'Favorite item Quantity'
            ]);

            $query->orderBy('id', 'asc')
                ->chunk(10000, function ($rows) use ($handle, $topItems) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->shipping_name,
                            $row->email,
                            $row->total_orders_customers,
                            $row->total_price_customers,
                            $topItems[strtolower($row->email)]['lineitem_name'] ?? '',
                            $topItems[strtolower($row->email)]['real_qty'] ?? ''
                        ]);
                    }
                });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename="crafty_customers.csv"',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }

    public function render()
    {
        $rows = Order::searchCustomer($this->search, $this->dateStart, $this->dateEnd)
            ->selectRaw( 'SUM(`total`)  AS total_price_customers' )
            ->selectRaw( 'COUNT(*)  AS total_orders_customers')
            ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
            ->groupBy('email')
            ->paginate($this->perPage);

        $emails = $rows->pluck('email')->toArray();

        $items = Item::query()->whereIn('customer_email', $emails)
            ->selectRaw('SUM(`lineitem_qty`) as `real_qty`, `lineitem_name`, LOWER(`customer_email`) as `email`')
            ->groupBy('customer_email', 'lineitem_sku')
            ->get();

        $topItems = $items->groupBy('email')
            ->map(fn ($items) => $items->sortByDesc('real_qty')->first())
            ->toArray();

        $columns = [
            [
                'title' => 'Name',
                'attribute' => 'shipping_name',
                'sort' => true,
            ],
            [
                'title' => 'E-mail address',
                'attribute' => 'email',
                'sortField' => 'email',
                'sort' => true,
            ],
            [
                'title' => 'Total orders',
                'attribute' => 'total_orders_customers',
                'sortField' => 'total_orders_customers',
                'sort' => true,
            ],
            [
                'title' => 'Money spent',
                'attribute' => 'total_price_customers',
                'sortField' => 'total_price_customers',
                'sort' => true,
            ],
            [
                'title' => 'Favorite item',
                'attribute' => 'email',
                'sort' => false,
                'mutator' => function ($email) use ($topItems) {
                    return $topItems[strtolower($email)]['lineitem_name'] ?? '';
                }
            ],
            [
                'title' => 'Favorite item Quantity',
                'attribute' => 'email',
                'sort' => false,
                'mutator' => function ($email) use ($topItems) {
                    return $topItems[strtolower($email)]['real_qty'] ?? '';
                }
            ],
        ];

        $columns[] = [
            'title' => 'tools',
            'sort' => false,
            'tools' => ['customer_orders',],
        ];


        return view('livewire.backstage.table', [
            'columns' => $columns,
            'resource' => 'customers',
            'rows' => $rows
        ]);
    }
}
