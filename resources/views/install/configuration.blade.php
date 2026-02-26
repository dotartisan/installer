@extends('install.layout')

@section('content')
    <h2>3. Configuration</h2>
    <hr>
    @include ('install.messages')
    <form method="POST" action="{{ route('installconfig.post') }}" class="form-horizontal">
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    Please enter your database connection details.
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="host">Host <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="db[host]" value="{{ old('db.host', '127.0.0.1') }}" id="host"
                            class="form-control {{ $errors->has('db.host') ? 'is-invalid' : '' }}"
                            placeholder="Enter database host" autofocus>

                        {!! $errors->first('db.host', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="port">Port <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="db[port]" value="{{ old('db.port', '3306') }}" id="port"
                            class="form-control {{ $errors->has('db.port') ? 'is-invalid' : '' }}"
                            placeholder="Enter database port">

                        {!! $errors->first('db.port', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="db-username">DB Username <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="db[username]" value="{{ old('db.username') }}" id="db-username"
                            class="form-control {{ $errors->has('db.username') ? 'is-invalid' : '' }}"
                            placeholder="Enter database user name">

                        {!! $errors->first('db.username', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="db-password">DB Password</label>
                    <div class="col-sm-9">
                        <input type="password" name="db[password]" value="{{ old('db.password') }}" id="db-password"
                            class="form-control" placeholder="Enter database password">
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="database">Database <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="db[database]" value="{{ old('db.database') }}" id="database"
                            class="form-control {{ $errors->has('db.database') ? 'is-invalid' : '' }}"
                            placeholder="Enter database name">

                        {!! $errors->first('db.database', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Please enter a username and password for the administration.</h5>
            </div>
            <div class="card-body">
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="admin-full-name">Full Name <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="admin[name]" value="{{ old('admin.name') }}" id="admin-full-name"
                            class="form-control {{ $errors->has('admin.name') ? 'is-invalid' : '' }}"
                            placeholder="Enter the name of application administrator">
                        {!! $errors->first('admin.name', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="admin-email">Email <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="admin[email]" value="{{ old('admin.email') }}" id="admin-email"
                            class="form-control {{ $errors->has('admin.email') ? 'is-invalid' : '' }}"
                            placeholder="Enter the email address for administrator account">
                        {!! $errors->first('admin.email', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="admin-password">Password <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="password" name="admin[password]" id="admin-password"
                            class="form-control {{ $errors->has('admin.password') ? 'is-invalid' : '' }}"
                            placeholder="Enter administrator password">
                        {!! $errors->first('admin.password', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="admin-confirm-password">Confirm Password <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="password" name="admin[password_confirmation]" id="admin-confirm-password"
                            class="form-control" placeholder="Confirm administrator password">
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Please enter your website details.</h5>
            </div>
            <div class="card-body p-b-0">
                <div class="form-group row mb-3">
                    <label class="form-label col-sm-3" for="app-name">Application Title<span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="website[app_name]" value="{{ old('website.app_name') }}"
                            id="app-name"
                            class="form-control {{ $errors->has('website.app_name') ? 'is-invalid' : '' }}"
                            placeholder="Enter application name">
                        {!! $errors->first('website.app_name', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row mb-3 has-validation">
                    <label class="form-label col-sm-3" for="app-email">Contact Email <span
                            class="text-danger small bold">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="website[app_email]" value="{{ old('website.app_email') }}"
                            id="app-email"
                            class="form-control {{ $errors->has('website.app_email') ? 'is-invalid' : '' }}"
                            placeholder="Enter website contact email">
                        {!! $errors->first('website.app_email', '<span class="invalid-feedback">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="content-buttons text-end">
            <button type="submit" class="btn btn-primary rounded-pill px-5 text-white btn-lg install-button">
                Install
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-5 text-white btn-lg loading-button d-none"
                disabled>
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const AppInstall = function() {
            const attachEvents = function() {
                    document.querySelector('.install-button').addEventListener('click', e => {
                        showLoader()
                    })
                    document.querySelector('form').addEventListener('submit', e => {
                        showLoader()
                    })
                },
                showLoader = function() {
                    const button = document.querySelector('.install-button')
                    const loader = document.querySelector('.loading-button')
                    button.classList.add('d-none')
                    loader.classList.remove('d-none')
                };

            return {
                init: function() {
                    attachEvents();
                }
            }
        }();

        document.addEventListener("DOMContentLoaded", function(event) {
            AppInstall.init();
        });
    </script>
@endpush
