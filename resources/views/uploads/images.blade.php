@extends('layouts.dashboard')

@section('title', 'Imagens')
@section('breadcrumb')
  <li><a href="{{ route('uploads.images') }}">Uploads</a></li>
@endsection
@section('breadcrumb-active', 'Imagens')

@section('content')
  @include('uploads._list', ['type' => 'image'])
@endsection
