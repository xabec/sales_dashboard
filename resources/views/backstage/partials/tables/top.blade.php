<div class="flex justify-between pb-4" >
    <h1 class="m-0 p-0">{{ ucfirst($name ?? $resource) }}</h1>

    <div class="flex justify-between">
        <select wire:model="perPage" class="form-select border border-gray-300 bg-gray-100 rounded-full text-gray-400">
            <option>10</option>
            <option>15</option>
            <option>25</option>
        </select>

        @if($hasSearch)
            <input wire:model="search" type="text" placeholder="Search..." class="bg-gray-100 border border-gray-300 rounded-full px-4 text-gray-400 ml-4">
        @endif

        @if($hasDateSearch)
            <input wire:model="dateStart" type="date" placeholder="Search..." class="bg-gray-100 border border-gray-300 rounded-full px-4 text-gray-400 ml-4">
            <input wire:model="dateEnd" type="date" placeholder="Search..." class="bg-gray-100 border border-gray-300 rounded-full px-4 text-gray-400 ml-4">
        @endif

        <button wire:click="export" class="btn btn-dark float-right">
            Export
        </button>
    </div>
</div>
