<tbody class="table">
@if(count($rows))
    @foreach($rows as $key => $row)
        <tr class="@if( ($key+1) % 2 === 0 ) alternate @endif">
            @foreach($columns as $column)
                @if( $column['title'] !== 'tools' )
                    <td class="">
                        @if (isset($column['mutator']) && is_callable($column['mutator']))
                            {{ $column['mutator']($row->{ $column['attribute'] ?? $column['title'] }) }}
                        @else
                            {{ $row->{ $column['attribute'] ?? $column['title'] } }}
                        @endif
                    </td>
                @else
                    <td class="">
                        @if( in_array('use', $column['tools'], true) )
                            <a href="{{ route('backstage.'.$resource.'.use', $row->id) }}" class="table-tool">
                                <i class="fas fa-play"></i>
                            </a>
                        @endif

                        @if (in_array('item_details', $column['tools'], true) )
                            <button class="btn btn-primary" type="button" data-toggle="modal"
                                    data-target="#item_details_{{ $row->order_number }}" aria-expanded="false"
                                    aria-controls="order_items_collapse">
                                Show order items
                            </button>

                            <div class="modal fade" id="item_details_{{ $row->order_number }}" tabindex="-1" role="dialog"
                                 aria-labelledby="item_details_label" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="item_details_label">Order items</h5>
                                            <button type="button" class="close" data-dismiss="item_details_{{ $row->order_number }}" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body modal-lg">
                                            @include('backstage.partials.tables.body-items')
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="item_details_{{ $row->order_number }}">Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script type="text/javascript">
                                $("button[data-dismiss=item_details_{{ $row->order_number }}]").click(function(){
                                    $('#item_details_{{ $row->order_number }}').modal('hide');
                                });
                            </script>
                        @endif


                        @if (in_array('order_details', $column['tools'], true) )
                            <button class="btn btn-primary" type="button" data-toggle="modal"
                                    data-target="#order_details_{{ $row->order_number }}" aria-expanded="false"
                                    aria-controls="order_details_collapse">
                                Expand order details
                            </button>

                            <div class="modal fade" id="order_details_{{ $row->order_number }}" tabindex="-1" role="dialog"
                                 aria-labelledby="order_details_label" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="order_details_label">Order items</h5>
                                            <button type="button" class="close" data-dismiss="order_details_{{ $row->order_number }}" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body modal-lg">
                                            @include('backstage.partials.tables.body-details')
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="order_details_{{ $row->order_number }}">Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <script type="text/javascript">
                                    $("button[data-dismiss=order_details_{{ $row->order_number }}").click(function(){
                                        $('#order_details_{{ $row->order_number }}').modal('hide');
                                    });
                                </script>

                        @endif

                            @if (in_array('customer_orders', $column['tools'], true) )
                                <button wire:click="$emit('OrdersFilter','{{$row->email}}')" class="btn btn-primary" type="button" data-toggle="modal"
                                        data-target="#customer_orders" aria-expanded="false"
                                        aria-controls="order_details_collapse">
                                    Show customer orders
                                </button>
                            @endif


                            @if (in_array('top_item_details', $column['tools'], true) )
                                <button wire:click="$emit('TopItemDetails','{{$row->lineitem_sku}}')" class="btn btn-primary" type="button" data-toggle="modal"
                                        data-target="#top_item_details" aria-expanded="false"
                                        aria-controls="top_item_details_collapse">
                                    Details
                                </button>
                            @endif

                    </td>
                @endif
            @endforeach
        </tr>
    @endforeach
@else
    <tr>
        <td class="text-center"
            colspan="{{ count($columns) }}">No data
        </td>
    </tr>
@endif

</tbody>
