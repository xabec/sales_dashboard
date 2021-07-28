<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_number
 * @property string $email
 * @property string $order_date
 * @property string $order_status
 * @property string|null $payment_date
 * @property string|null $fulfillment_date
 * @property string $currency
 * @property string $subtotal
 * @property string $shipping_method
 * @property string $shipping_cost
 * @property string|null $tax_method
 * @property string $taxes
 * @property string $total
 * @property string|null $coupon_code
 * @property string|null $coupon_code_name
 * @property string $discount
 * @property string|null $billing_name
 * @property string|null $billing_country
 * @property string|null $billing_address_street
 * @property string|null $billing_address_county
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_zip
 * @property string|null $billing_number
 * @property string $shipping_name
 * @property string $shipping_country
 * @property string|null $shipping_address_street
 * @property string|null $shipping_address_county
 * @property string|null $shipping_city
 * @property string|null $shipping_state
 * @property string|null $shipping_zip
 * @property string|null $shipping_phone
 * @property string|null $gift_cards
 * @property string $payment_method
 * @property string|null $tracking_number
 * @property string|null $special_instructions
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingAddressCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingAddressStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponCodeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFulfillmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereGiftCards($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingAddressCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingAddressStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSpecialInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTaxMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTrackingNumber($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['order_number', 'import_id', 'email', 'order_date', 'order_status', 'fullfilment_status',
        'payment_status', 'payment_date', 'fulfillment_date', 'currency',
        'subtotal', 'shipping_method', 'shipping_cost', 'tax_method', 'taxes', 'total', 'coupon_code', 'coupon_code_name',
        'discount', 'billing_name', 'billing_country', 'billing_address_street', 'billing_address_county', 'billing_city',
        'billing_state', 'billing_zip', 'billing_number', 'shipping_name', 'shipping_country', 'shipping_address_street',
        'shipping_address_county', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_phone', 'gift_cards',
        'payment_method', 'tracking_number', 'special_instructions'];
    public $timestamps = false;

    public static function search($data, $start, $end, $email = null)
    {
        return Order::select('orders.*')
            ->where(function ($query) use ($data) {
                $query->when($data, function ($query) use ($data) {
                    $query->orWhere('order_number', 'LIKE', "%$data%");
                    $query->orWhere('email', 'LIKE', "%$data%");
                    $query->orWhere('shipping_name', 'LIKE', "%$data%");
                    $query->orWhere('lineitem_sku', 'LIKE', "%$data%");
                });
            })
            ->where(function ($query) use ($start, $end) {
                $query->when($start, function ($query) use ($start, $end) {
                    $query->Where('order_date', '>=', $start);
                });

                $query->when($end, function ($query) use ($end, $start) {
                    $query->Where('order_date', '<=', $end);
                });
            })
            ->when($email, function ($query) use($email) {
               $query->where('email', $email);
            });

    }

    public static function searchCustomer($data, $start, $end)
    {
        return Order::select('orders.*')
            ->where(function ($query) use ($data) {
                $query->when($data, function ($query) use ($data) {
                    $query->orWhere('order_number', 'LIKE', "%$data%");
                    $query->orWhere('email', 'LIKE', "%$data%");
                    $query->orWhere('shipping_name', 'LIKE', "%$data%");
                });
            })
            ->where(function ($query) use ($start, $end) {
                $query->when($start, function ($query) use ($start, $end) {
                    $query->Where('order_date', '>=', $start);
                });

                $query->when($end, function ($query) use ($end, $start) {
                    $query->Where('order_date', '<=', $end);
                });
            });

    }

    use HasFactory;
}
