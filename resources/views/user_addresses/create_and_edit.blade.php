@extends('layouts.app')
@section('title', 'Add new post address')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="text-center">
                    Add New Post Address
                </h2>
            </div>
            <div class="panel-body">
                <!--Start output back-end error-->
                @if (count($errors)>0)
                    <div class="alert alert-danger">
                        <h4>There is some errors issued:</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>
                                    <i class="glyphicon glyphicon-remove"></i>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!--End output back-end error-->
                <!--inline-template 通过内联方式引入组件 -->
                <user-addresses-create-and-edit inline-template>
                    <form class="form-horizontal" role="form" action="{{ route('user_addresses.store') }}" method="post">
                        <!--import 'csrf token' character-->
                        {{ csrf_field() }}

                        <select-district @change="onDistrictChanged" inline-template>
                            <div class="form-group">
                                <label class="control-label col-sm-2">
                                    Province
                                </label>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="provinceId">
                                        <option value="">Choose Province</option>
                                        <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="cityId">
                                        <option value="">Choose City</option>
                                        <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="districtId">
                                        <option value="">Choose District</option>
                                        <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                            </div>
                        </select-district>
                        <!--Insert 3 hidden string-->
                        <!--Link them with (user-addresses-create-and-edit) component through v-model-->
                        <!--So that the value here will be changed following the component value changed-->
                        <input type="hidden" name="province" v-model="province">
                        <input type="hidden" name="city" v-model="city">
                        <input type="hidden" name="district" v-model="district">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Address Detail</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Zip</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="zip" value="{{ old('zip', $address->zip) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </user-addresses-create-and-edit>
            </div>
        </div>
    </div>
</div>
@endsection