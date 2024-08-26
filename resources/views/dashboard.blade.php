@extends('layouts.app')
@section('content')
@include('layouts.headers.cards')
@push('head')
<?= __yesset(['dist/css/dashboard.css'],true)?>
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if (!getAppSettings('cron_setup_done_at'))
                <div class="alert alert-danger"><i class="fa fa-info-circle"></i> {{  __tr('Cron setup is required') }}</div>
            @endif
            @if (!getAppSettings('pusher_app_id'))
                <div class="alert alert-danger"><i class="fa fa-info-circle"></i> {{  __tr('Pusher configuration is required') }}</div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col mb-5 mb-xl-0">
            <div class="card bg-gradient-default shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-light ls-1 mb-1">{{  __tr('Last 12 Months') }}</h6>
                            <h2 class="text-white mb-0">{{  __tr('New Vendor Registrations') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chart">
                        <canvas id="lwNewVendorRegistrationGraph" class="chart-canvas" height="300"></canvas>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow mb-5">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{  __tr('New Vendors') }}</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('central.vendors') }}" class="btn btn-sm btn-primary">{{  __tr('See all') }}</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{  __tr('Vendor Title') }}</th>
                                <th scope="col">{{  __tr('Registered on') }}</th>
                                <th scope="col">{{  __tr('Vendor Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($newVendors as $newVendor)
                            <tr>
                                <th scope="row">  <a href="{{ route('vendor.dashboard',['vendorIdOrUid'=>$newVendor->_uid])}}">{{ $newVendor->title }}</a></th>
                                <td>{{ formatDate($newVendor->created_at) }}</td>
                                <td>{{ configItem("status_codes." . $newVendor->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<?= __yesset(['dist/js/dashboard.js'],true)?>
@endpush
@push('appScripts')
<script>
        (function($) {
        'use strict';
    var ctx1 = document.getElementById("lwNewVendorRegistrationGraph").getContext("2d");
    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
    new Chart(ctx1, {
      type: "line",
      data: {
        labels: @json(array_pluck($vendorRegistrations, 'month_name')),
        datasets: [{
          label: "{{ __tr('New Vendor Registrations') }}",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#5e72e4",
          backgroundColor: gradientStroke1,
          borderWidth: 3,
          fill: true,
          data: @json(array_pluck($vendorRegistrations, 'vendors_count')),
          maxBarThickness: 6

        }],
      },
      options: {
        locale : window.appConfig.locale,
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#fbfbfb'
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#ccc',
              padding: 20
            }
          },
        },
      },
    });
})(jQuery);
  </script>
@endpush
