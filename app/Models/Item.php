<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $order_id
 * @property string $customer_email
 * @property string $lineitem_name
 * @property string $lineitem_sku
 * @property string|null $lineitem_options
 * @property string|null $lineitem_addons
 * @property int $lineitem_qty
 * @property string $lineitem_price
 * @property string $lineitem_type
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemAddons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereLineitemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereOrderId($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    protected $table = 'items';
    protected $fillable = ['order_id','customer_email', 'lineitem_name', 'lineitem_sku', 'lineitem_options',
        'lineitem_addons', 'lineitem_qty', 'lineitem_price', 'lineitem_type'];
    public $timestamps = false;

    public static function search($data, $start, $end)
    {
        return Item::select('items.*')
            ->where(function ($query) use ($data) {
                $query->when($data, function ($query) use ($data) {
                    $query->orWhere('orders.order_number', 'LIKE', "%$data%");
                    $query->orWhere('lineitem_sku', 'LIKE', "%$data%");
                    $query->orWhere('lineitem_options', 'LIKE', "%$data%");
                    $query->orWhere('lineitem_price', 'LIKE', "%$data%");
                });
            })
            ->where(function ($query) use ($start, $end){
                $query->when($start, function ($query) use ($start) {
                    $query->Where('order_date', '>=', $start);
                });

                $query->when($end, function ($query) use ($end) {
                    $query->Where('order_date', '<=', $end);
                });
            });
    }

    use HasFactory;
}
