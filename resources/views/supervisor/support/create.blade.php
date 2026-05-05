@extends("layouts.supervisor-app")
@section("title", "New Support Ticket")
@section("page-title", "New Support Ticket")
@section("content")
@php
    $indexRoute = "supervisor.support.index";
    $storeRoute = "supervisor.support.store";
@endphp
@include("_support._create_content")
@endsection