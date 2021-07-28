<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Illuminate\Support\Arr;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemDetails extends Component
{
    public $sizes = [];
    public $materials = [];
    public $packs = [];
    public $name = '';

    protected $listeners = [
        'TopItemDetails' => 'TopItemDetails',
    ];

    public $sku;

    public function TopItemDetails($sku)
    {
        $this->sku = $sku;
    }

    public function export()
    {

        return new StreamedResponse(function () {
            // Open output stream
            $handle = fopen('php://output', 'wb');

            // Add CSV headers
            fputcsv($handle, [
                $this->name . ' - ' . $this->sku,
                'Size',
                'Count',
                '%',
                '',
                'Material',
                'Count',
                '%',
                '',
                'Pack',
                'Count',
                '%'
            ]);


            $lines = [];

            $totalSizes = array_sum($this->sizes);
            $totalMaterials = array_sum($this->materials);
            $totalPacks = array_sum($this->packs);


            $totalLines = max($totalSizes, $totalMaterials, $totalPacks);

            $lines = array_map(function () {
                return array_fill(0, 12, '');
            }, array_fill(0, $totalLines, []));

            $x = 0;
            foreach ($this->sizes as $size => $count) {
                $lines[$x][1] = $size;
                $lines[$x][2] = $count;
                $lines[$x][3] = number_format($count / $totalSizes * 100, 2);

                $x++;
            }

            $x = 0;
            foreach ($this->materials as $material => $count) {
                $lines[$x][5] = $material;
                $lines[$x][6] = $count;
                $lines[$x][7] = number_format($count / $totalMaterials * 100, 2);

                $x++;
            }

            $x = 0;
            foreach ($this->packs as $pack => $count) {
                $lines[$x][9] = $pack;
                $lines[$x][10] = $count;
                $lines[$x][11] = number_format($count / $totalPacks * 100, 2);

                $x++;
            }


            foreach ($lines as $line) {
                fputcsv($handle, $line);
            }

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename="' .  $this->name . ' - ' . $this->sku . '.csv"',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }

    public function render()
    {

        $this->packs = [];
        $this->materials = [];
        $this->sizes = [];

        if ($this->sku) {
            $items = Item::whereLineitemSku($this->sku)
                ->get();

            if ($items->isNotEmpty()) {
                $this->name = $items->first()->lineitem_name;
            }

            foreach ($items as $item) {
                $options = $item->lineitem_options;

                $size = null;
                if (preg_match('/sizes?:([^|]+)/i', $options, $matches)) {
                    $size = $matches[1];

                    if (isset($this->sizes[$size])) {
                        $this->sizes[$size] += $item->lineitem_qty;
                    } else {
                        $this->sizes[$size] = $item->lineitem_qty;
                    }
                }

                if (preg_match('/sizing:([^|]+)/i', $options, $matches)) {
                    $size = $matches[1];

                    if (isset($this->sizes[$size])) {
                        $this->sizes[$size] += $item->lineitem_qty;
                    } else {
                        $this->sizes[$size] = $item->lineitem_qty;
                    }
                }


                if (preg_match('/materials?:([^|]+)/i', $options, $matches)) {
                    $material = $matches[1];

                    if (isset($this->materials[$material])) {
                        $this->materials[$material] += $item->lineitem_qty;
                    } else {
                        $this->materials[$material] = $item->lineitem_qty;
                    }
                }


                if (preg_match('/colour:([^|]+)/i', $options, $matches)) {
                    $material = $matches[1];

                    if (isset($this->materials[$material])) {
                        $this->materials[$material] += $item->lineitem_qty;
                    } else {
                        $this->materials[$material] = $item->lineitem_qty;
                    }
                }

                if (preg_match('/Pack size:([^|]+)/i', $options, $matches)) {
                    $pack = $matches[1];

                    if (isset($this->packs[$pack])) {
                        $this->packs[$pack] += $item->lineitem_qty;
                    } else {
                        $this->packs[$pack] = $item->lineitem_qty;
                    }
                }

                if (preg_match('/Pk size:([^|]+)/i', $options, $matches)) {
                    $pack = $matches[1];

                    if (isset($this->packs[$pack])) {
                        $this->packs[$pack] += $item->lineitem_qty;
                    } else {
                        $this->packs[$pack] = $item->lineitem_qty;
                    }
                }

                if (preg_match('/pack:([^|]+)/i', $options, $matches)) {
                    $pack = $matches[1];

                    if (isset($this->packs[$pack])) {
                        $this->packs[$pack] += $item->lineitem_qty;
                    } else {
                        $this->packs[$pack] = $item->lineitem_qty;
                    }
                }
            }
        }


        arsort($this->sizes);
        arsort($this->materials);
        arsort($this->packs);


        return view('livewire.item-details', ['sku' => $this->sku, 'name' => $this->name] + compact(['sizes' => $this->sizes, 'materials' => $this->materials, 'packs' => $this->packs]));
    }
}
