@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ 'Items' }}</div>

                    <div class="card-body">
                        <livewire:backstage.item-table/>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="top_item_details" tabindex="-1" role="dialog"
         aria-labelledby="top_item_details_label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <livewire:item-details/>
            </div>
        </div>
    </div>
@endsection
