<?php

namespace App\Charts;

use App\Models\Order;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesChart extends BaseChart
{

    public function handler(Request $request): Chartisan
    {

        $fromDate = null;
        $tillDate = null;

        if ($request->get('from')) {
            $fromDate = Carbon::parse($request->get('from'));
        }
        if ($request->get('till')) {
            $tillDate = Carbon::parse($request->get('till'));
        }

        $fromDate->setHour(0)->setMinute(0)->setSecond(0);
        $tillDate->setHour(23)->setMinute(59)->setSecond(59);

        $daysRangeChosen = $tillDate->diffInDays($fromDate);

        $previousRangeStarts = $fromDate->copy()->subDays($daysRangeChosen);


        $dataset = Order::whereBetween('order_date', [$fromDate, $tillDate])
            ->leftjoin('items', 'items.order_id', 'orders.order_number')
            ->selectRaw('SUM(`lineitem_qty`) as `total`, COUNT(`order_number`) as `orders`, DATE(`order_date`) as `label`')
            ->groupByRaw('DATE(order_date)')
            ->get();

        $datasetBefore = Order::whereBetween('order_date', [$previousRangeStarts, $fromDate])
            ->leftjoin('items', 'items.order_id', 'orders.order_number')
            ->selectRaw('SUM(`lineitem_qty`) as `total`, COUNT(`order_number`) as `orders`, DATE(`order_date`) as `label`')
            ->groupByRaw('DATE(order_date)')
            ->get()
            ->groupBy('label');

        $labels = $dataset->pluck('label')->toArray();


        $itemsBefore = [];

        for ($i=0, $iMax = count($labels); $i < $iMax; ++$i) {
            $dateBefore = Carbon::parse($labels[$i])->subDays($daysRangeChosen)->toDateString();
            $labels[$i] .= ' vs ' . $dateBefore;

            if ($datasetBefore->has($dateBefore)) {
                $itemsBefore[] = $datasetBefore[$dateBefore]->first()->total;

            }

        }

        return Chartisan::build()
            ->labels($labels)
            ->dataset('Items sold', $dataset->pluck('total')->toArray())
            ->dataset("Items sold {$daysRangeChosen} days ago", $itemsBefore);
    }
}
