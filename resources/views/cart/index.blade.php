@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h2>Shopping Cart</h2>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Your cart is empty
            </div>
            <a href="{{ route('courses') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
