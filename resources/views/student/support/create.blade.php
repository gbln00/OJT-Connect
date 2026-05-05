@extends("layouts.student-app")
@section("title", "New Support Ticket")
@section("page-title", "New Support Ticket")
@section("content")
@php
    $indexRoute = "student.support.index";
    $storeRoute = "student.support.store";
@endphp
@include("_support._create_content")
@endsection