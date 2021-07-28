<div>
    @include('backstage.partials.tables.top')
    <div class="row">
        <div class="col-12">
            <div class="align-middle inline-block min-w-full overflow-hidden">
                <table class="table table-striped">
                    @include('backstage.partials.tables.headers')
                    @include('backstage.partials.tables.body')
                </table>
            </div>
        </div>
    </div>
    @include('backstage.partials.tables.footer')
</div>
