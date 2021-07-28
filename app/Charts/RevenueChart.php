<?php

namespace App\Charts;

use App\Models\Order;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RevenueChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
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
            ->selectRaw('SUM(`total`) as `total`, COUNT(`order_number`) as `orders`, DATE(`order_date`) as `label`')
            ->groupByRaw('DATE(order_date)')
            ->get();

        $datasetBefore = Order::whereBetween('order_date', [$previousRangeStarts, $fromDate])
            ->selectRaw('SUM(`total`) as `total`, COUNT(`order_number`) as `orders`, DATE(`order_date`) as `label`')
            ->groupByRaw('DATE(order_date)')
            ->get()
            ->groupBy('label');

        $labels = $dataset->pluck('label')->toArray();


        $revenueBefore = [];

        for ($i=0, $iMax = count($labels); $i < $iMax; ++$i) {
            $dateBefore = Carbon::parse($labels[$i])->subDays($daysRangeChosen)->toDateString();
            $labels[$i] .= ' vs ' . $dateBefore;

            if ($datasetBefore->has($dateBefore)) {
                $revenueBefore[] = $datasetBefore[$dateBefore]->first()->total;
            }

        }

        return Chartisan::build()
            ->labels($labels)
            ->dataset('Revenue', $dataset->pluck('total')->toArray())
            ->dataset("Revenue {$daysRangeChosen} days ago", $revenueBefore);
    }
}
