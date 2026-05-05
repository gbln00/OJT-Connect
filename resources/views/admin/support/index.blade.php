@extends("layouts.app")
@section("title", "Support & Feedback")
@section("page-title", "Support & Feedback")
@section("content")
@php
    $createRoute = "admin.support.create";
    $showRoute   = "admin.support.show";
@endphp
@include("_support._index_content")
@endsection