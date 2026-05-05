@extends("layouts.app")
@section("title", "New Support Ticket")
@section("page-title", "New Support Ticket")
@section("content")
@php
    $indexRoute = "admin.support.index";
    $storeRoute = "admin.support.store";
@endphp
@include("_support._create_content")
@endsection