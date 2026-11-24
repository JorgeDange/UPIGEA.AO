@extends('layouts.dashboard')

@section('title', 'Documentos')
@section('breadcrumb')
  <li><a href="{{ route('uploads.documents') }}">Uploads</a></li>
@endsection
@section('breadcrumb-active', 'Documentos')

@section('content')
  @include('uploads._list', ['type' => 'document'])
@endsection
