@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ 'Customers' }}</div>

                    <div class="card-body">
                        <livewire:backstage.customers-table/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customer_orders" tabindex="-1" role="dialog"
                                     aria-labelledby="customer_orders_label" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="customer_orders_label">Customer orders</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body modal-xl">
                                                @include('backstage.partials.tables.body-orders')
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
@endsection

@push('js')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $('#customer_orders').on('hidden.bs.modal', function (e) {
                if($(e.target).attr('id') == 'customer_orders') {
                    Livewire.emit('OrdersClear');
                } else {
                    $('body').addClass('modal-open');
                }
            })
        });
    </script>
@endpush

