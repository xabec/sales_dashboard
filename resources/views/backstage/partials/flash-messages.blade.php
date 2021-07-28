<script>

    @if( Session::has('success') )
        swal({
            title: "Great!",
            text: "{{ Session::get('success') }}",
            timer: 5000,
            button: false,
            icon: 'success'
        });
    @endif

    @if( Session::has('error') )
    swal({
        title: "Error!",
        text: "{{ Session::get('error') }}",
        timer: 5000,
        button: false,
        icon: 'error'
    });
    @endif

</script>
