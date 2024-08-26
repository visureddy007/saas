@extends('layouts.app', ['title' => __tr('Proceed to Pay for Subscription')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Proceed to Pay for Subscription'),
    'description' => '',
    'class' => 'col-lg-7'
    ])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
           <div class="card col-sm-12 ">
              <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="card">
                            <div class="card-header" style="background-color: #2bac32;">
                                <h3 class="text-white">Payment Successful!</h3>
                            </div>
                            <div class="card-body">
                                <p class="lead">Thank you for your purchase.</p>
                                <p>Your payment has been successfully processed.</p>
                                <p>Transaction ID: <strong class="text-dark ">{{ $txnReferenceId }}</strong></p>
                                <a href="{{ route('subscription.read.show') }}" class="btn btn-primary mt-3">Go to Subscription</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
@endsection





</body>
</html>
