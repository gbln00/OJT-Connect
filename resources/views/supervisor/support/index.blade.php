@extends("layouts.supervisor-app")
@section("title", "Support & Feedback")
@section("page-title", "Support & Feedback")
@section("content")
@php
    $createRoute = "supervisor.support.create";
    $showRoute   = "supervisor.support.show";
@endphp
@include("_support._index_content")
@endsection