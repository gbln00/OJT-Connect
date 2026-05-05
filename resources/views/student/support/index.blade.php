@extends("layouts.student-app")
@section("title", "Support & Feedback")
@section("page-title", "Support & Feedback")
@section("content")
@php
    $createRoute = "student.support.create";
    $showRoute   = "student.support.show";
@endphp
@include("_support._index_content")
@endsection