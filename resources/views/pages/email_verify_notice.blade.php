@extends('layouts.app')
@section('title', 'Notice')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">Notification</div>
    <div class="panel-body text-center">
        <h1>Please Verify Your Email Address</h1>
        <a class="btn btn-primary" href="{{ route('email_verification.send') }}">Send Email Again</a>
    </div>
</div>
@endsection
