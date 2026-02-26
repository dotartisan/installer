@extends('install.layout')

@section('content')
    <h2>4. Complete {{ request()->is('install/complete') }}</h2>
    <hr>
    <div class="card install-complete">
        <div class="installation-message text-center">
            <i class="an an-check-double mb-4" aria-hidden="true"></i>
            <h3>Yay! Installation completed successfully!</h3>
        </div>
        <div class="card-body visit-wrapper text-center clearfix">
            <div class="row">
                <div class="col-sm-6">
                    <a href="{{ url('/') }}" class="visit text-center" target="_blank">
                        <div class="install-icon">
                            <i class="an an-lcd" aria-hidden="true"></i>
                        </div>

                        <h5>Go to Your Frontend</h5>
                    </a>
                </div>

                <div class="col-sm-6">
                    <a href="{{ url('admin') }}" class="visit text-center" target="_blank">
                        <div class="install-icon">
                            <i class="an an-settings" aria-hidden="true"></i>
                        </div>

                        <h5>Login to Administration</h5>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
