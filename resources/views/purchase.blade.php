@extends('installer::layout')

@section('content')
    <h2>2. Verify Purchase</h2>
    <hr>

    @include('installer::messages')

    @php
        $isEnvato = config('artisan.source') === 'envato';
        $canProceed = $requirement->satisfied() && $verifyPurchase->satisfied();
    @endphp

    @if ($isEnvato)
        <div class="box">
            <div class="configure-form">
                Before you continue with installation we need to verify your license.
                Please signin with Envato for validation.
            </div>
        </div>

        <div class="content-buttons mt-3 text-end">
            <a href="{{ $canProceed ? route('installconfig.get') : route('verify.redirect') }}"
                class="btn btn-primary rounded-pill px-5 text-white btn-lg">
                {{ $canProceed ? 'Continue' : 'Signin with Envato' }}
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('verify.register') }}">
            <div class="box">
                <div class="configure-form">
                    @csrf

                    @if ($canProceed)
                        Purchase verified successfully, please continue to next step.
                    @else
                        <div class="mb-3">
                            <label for="code" class="form-label">Purchase Code</label>
                            <input type="text" class="form-control" id="code" name="code"
                                value="{{ old('code', session('code', '')) }}" required
                                placeholder="Please enter your purchase code to verify your copy of MonsterTools.">
                        </div>
                    @endif

                </div>
            </div>
            <div class="content-buttons mt-3 text-end">
                @if ($canProceed)
                    <a href="{{ route('installconfig.get') }}" class="btn btn-primary rounded-pill px-5 text-white btn-lg">
                        Continue
                    </a>
                @else
                    <button type="submit" class="btn btn-primary rounded-pill px-5 text-white btn-lg">
                        Verify
                    </button>
                @endif
            </div>
        </form>
    @endif
@endsection
