<?php

namespace App\Http\Livewire\Backstage;

use App\Models\Item;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemTable extends TableComponent
{
    public $sortField = 'total_price';

    public function export()
    {
        $query = Item::search($this->search, $this->dateStart, $this->dateEnd)
            ->groupBy('lineitem_sku')
            ->leftJoin('orders', 'orders.order_number', 'items.order_id')
            ->selectRaw( 'SUM(`lineitem_qty`)  AS total_qty' )
            ->selectRaw( 'SUM(`lineitem_price`) AS total_price' )
            ->selectRaw( '(SELECT COUNT(DISTINCT `order_id`) FROM `items` as `tmp` WHERE `tmp`.`lineitem_sku`=`items`.`lineitem_sku`) as `total_orders`' )
            ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC');

        return new StreamedResponse(function () use ($query) {
            // Open output stream
            $handle = fopen('php://output', 'wb');

            // Add CSV headers
            fputcsv($handle, [
                'Name',
                'Item SKU',
                'Quantity Sold',
                'Total sold for',
                'Unique orders'
            ]);

            $query->orderBy('id', 'asc')
                ->chunk(10000, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->lineitem_name,
                            $row->lineitem_sku,
                            $row->total_qty,
                            $row->total_price,
                            $row->total_orders
                        ]);
                    }
                });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename="crafty_items.csv"',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }

    public function render()
    {
        $columns = [
            [
                'title' => 'Name',
                'attribute' => 'lineitem_name',
                'sort' => true,
            ],
            [
                'title' => 'Item SKU',
                'attribute' => 'lineitem_sku',
                'sortField' => 'lineitem_sku',
                'sort' => true,
            ],
            [
                'title' => 'Quantity Sold',
                'attribute' => 'total_qty',
                'sortField' => 'total_qty',
                'sort' => true,
            ],
            [
                'title' => 'Total sold for',
                'attribute' => 'total_price',
                'sortField' => 'total_price',
                'sort' => true,
            ],
            [
                'title' => 'Unique orders',
                'attribute' => 'total_orders',
                'sortField' => 'total_orders',
                'sort' => false,
            ]

        ];

        $columns[] = [
            'title' => 'tools',
            'sort' => false,
            'tools' => ['top_item_details'],
        ];

        return view('livewire.backstage.table', [
            'columns' => $columns,
            'resource' => 'items',
            'rows' => Item::search($this->search, $this->dateStart, $this->dateEnd)
                ->groupBy('lineitem_sku')
                ->leftJoin('orders', 'orders.order_number', 'items.order_id')
                ->selectRaw( 'SUM(`lineitem_qty`)  AS total_qty' )
                ->selectRaw( 'SUM(`lineitem_price`) AS total_price' )
                ->selectRaw( '(SELECT COUNT(DISTINCT `order_id`) FROM `items` as `tmp` WHERE `tmp`.`lineitem_sku`=`items`.`lineitem_sku`) as `total_orders`' )
                ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
                ->paginate($this->perPage),
        ]);
    }
}
