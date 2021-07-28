@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload Orders</div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('upload.store') }}">
                        @csrf
                        <div class="custom-file" id="customFile" lang="es">
                            <input type="file" class="custom-file-input" id="orders" name="orders" aria-describedby="fileHelp">
                            <label class="custom-file-label" for="exampleInputFile">
                                Select file...
                            </label>

                            @if ($errors->has('orders'))
                                <span class="text-danger">{{ $errors->first('orders') }}</span>
                            @endif

                            <button type="submit" name="upload" value="upload" id="upload" class="btn btn-block btn-dark mt-3"><i class="fa fa-fw fa-upload"></i> Upload</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script type="text/javascript">
    document.querySelector('.custom-file-input').addEventListener('change',function(e){
        var fileName = document.getElementById("orders").files[0].name;
        var nextSibling = e.target.nextElementSibling
        nextSibling.innerText = fileName
    })
    </script>
@endpush
