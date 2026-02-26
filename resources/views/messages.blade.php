<div class="notifications-wrap">
    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            @if (count($errors) > 1)
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            @else
              @foreach ($errors->all() as $error)
                {{ $error }}
              @endforeach
            @endif
            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>
