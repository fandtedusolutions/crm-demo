@extends('layouts.mantis')

@section('title', 'Send Mail — ' . $convertedLead->name)

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Send Mail — {{ $convertedLead->name }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.support-bosse-converted-leads.index') }}">Board of Open Schooling and Skill Education Support</a></li>
                    <li class="breadcrumb-item">Send Mail</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('admin.converted-leads.send-course-mail-form')
            </div>
        </div>
    </div>
</div>
@endsection

@if(empty($error))
@push('scripts')
@include('admin.converted-leads.partials.send-course-mail-form-scripts')
@endpush
@endif
