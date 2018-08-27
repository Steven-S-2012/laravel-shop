@extends('layouts.app')
@section('title', 'Post Address List')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                Post Address List
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
                            <button class="btn btn-primary">Modify</button>
                            <button class="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection