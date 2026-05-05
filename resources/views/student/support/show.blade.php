@extends('layouts.student-app')
@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)
@section('content')
@php
    $indexRoute  = 'student.support.index';
    $replyRoute  = 'student.support.reply';
    $closeRoute  = 'student.support.close';
    $createRoute = 'student.support.create';
@endphp
@include('_support._show_content')
@endsection