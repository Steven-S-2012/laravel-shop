@extends('layouts.app')
@section('title', 'Operation successflly.')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">Operation successfully</div>
    <div class="panel-body text-center">
        <h1>{{ $msg }}</h1>
        <a class="btn btn-primary" href="{{ route('root') }}">Home</a>
    </div>
</div>
@endsection