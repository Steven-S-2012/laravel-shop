@extends('layouts.app')
@section('title', 'Post Address List')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Post Address List
                    <a href="{{ route('user_addresses.create') }}" class="pull-right">Add</a>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Consignee</th>
                            <th>Address</th>
                            <th>Zip</th>
                            <th>Phone</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($addresses as $address)
                        <tr>
                            <td>{{ $address->contact_name }}</td>
                            <td>{{ $address->full_address }}</td>
                            <td>{{ $address->zip }}</td>
                            <td>{{ $address->contact_phone }}</td>
                            <td>
                                <a href="{{ route('user_addresses.edit', ['user_address' => $address->id]) }}" class="btn btn-primary">
                                    Modify
                                </a>
                                <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">Delete</button>

                                {{--<form action="{{ route('user_addresses.destroy', ['user_address' => $address->id]) }}"--}}
                                        {{--method="post" style="display:inline-block">--}}
                                    {{--{{ csrf_field() }}--}}
                                    {{--{{ method_field('DELETE') }}--}}
                                    {{--<button class="btn btn-danger">Delete</button>--}}
                                {{--</form>--}}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
//end html section
@endsection

@section('scriptsAfterJs')
<script>
    $(document).ready(function() {
        //Event: click delete button
        $('.btn-del-address').click(function() {
            //obtain the value of data-id from btn -- addressID
            var id = $(this).data('id');

            //invoke sweetalert
            swal({
                title: "Delete this address?",
                icon: "warning",
                buttons: ['Cancel', 'OK'],
                dangerMode: true,
            })
            .then(function(willDelete) {
                //click btn will trigger this callback
                //OK: willDelete will be true, otherwise will be false
                //Cancel: nothing
                if(!willDelete) {
                    return;
                }

                //call delete api, make url by using id
                axios.delete('/user_addresses/' + id)
                    .then(function() {
                        //Reloading pages after successfully required
                        location.reload();
                    })
            });
        });
    });
</script>
@endsection