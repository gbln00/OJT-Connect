@extends("layouts.coordinator-app")
@section("title", "Support & Feedback")
@section("page-title", "Support & Feedback")
@section("content")
@php
    $createRoute = "coordinator.support.create";
    $showRoute   = "coordinator.support.show";
@endphp
@include("_support._index_content")
@endsection