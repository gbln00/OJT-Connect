@extends("layouts.coordinator-app")
@section("title", "New Support Ticket")
@section("page-title", "New Support Ticket")
@section("content")
@php
    $indexRoute = "coordinator.support.index";
    $storeRoute = "coordinator.support.store";
@endphp
@include("_support._create_content")
@endsection