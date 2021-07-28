@foreach($items as $item)
    @if($item->order_id == $row->order_number)
        Lineitem Name: <b>{{ $item-> lineitem_name  }}</b><br>
        Lineitem SKU: <b>{{ $item-> lineitem_sku  }}</b><br>
        Lineitem options: <b>{{ $item-> lineitem_options  }}</b><br>
        Lineitem addons: <b>{{ $item-> lineitem_addons  }}</b><br>
        Lineitem quantity: <b>{{ $item-> lineitem_qty  }}</b><br>
        Lineitem price: <b>{{ $item-> lineitem_price  }}€</b><br>
        Lineitem type: <b>{{ $item-> lineitem_type  }}</b><br>
        <b>------------------------------</b><br>
    @endif
@endforeach
