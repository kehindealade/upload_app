@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>{{ __('Welcome, ') . Auth::user()->name }}</h5>
                        <div>
                            <img src="{{ asset('profile_pictures/' . Auth::user()->photo_url)}}" alt="photo" height="150" width="150" class="img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
