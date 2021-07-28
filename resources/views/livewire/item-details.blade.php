<div>
    <div class="modal-header">
        <h5 class="modal-title" id="customer_orders_label">{{ $name }} - {{ $sku }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body modal-xl">
        @if (! empty($sizes))
            <h3>Top Sizes</h3>

            @php
                $totalSizes = array_sum($sizes);
            @endphp

            <table class="table table-striped">
                <thead>
                <th>Size</th>
                <th>Count</th>
                <th>%</th>
                </thead>

                <tbody>
                @foreach($sizes as $size => $count)
                    <tr>
                        <td>{{ $size }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ number_format($count / $totalSizes * 100, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if (! empty($materials))
            <h3>Materials</h3>

            @php
                $topMaterials = array_sum($materials);
            @endphp

            <table class="table table-striped">
                <thead>
                <th>Material</th>
                <th>Count</th>
                <th>%</th>
                </thead>

                <tbody>
                @foreach($materials as $material => $count)
                    <tr>
                        <td>{{ $material }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ number_format($count / $topMaterials * 100, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if (! empty($packs))
            <h3>Packs</h3>

            @php
                $totalPacks = array_sum($packs);
            @endphp

            <table class="table table-striped">
                <thead>
                <th>Pack</th>
                <th>Count</th>
                <th>%</th>
                </thead>

                <tbody>
                @foreach($packs as $pack => $count)
                    <tr>
                        <td>{{ $pack }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ number_format($count / $totalPacks * 100, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div class="modal-footer">
        <button wire:click="export" class="btn btn-dark float-left">
            Export
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
        </button>
    </div>



</div>
