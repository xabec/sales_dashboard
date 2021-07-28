<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Sales extends Component
{
    public $revenue, $dateStart, $dateEnd, $test;

    public function render()
    {
        $this->revenue = Order::whereBetween('order_date', [Carbon::now()->startOfMonth()->startOfDay()->subMonth(5), Carbon::now()->endOfMonth()->endOfDay()])
            ->sum('total');

        return view('livewire.sales',[
            'test' => $this->test
        ]);
    }
}
