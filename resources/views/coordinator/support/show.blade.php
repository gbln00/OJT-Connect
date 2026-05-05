@extends('layouts.coordinator-app')
@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)
@section('content')
@php
    $indexRoute  = 'coordinator.support.index';
    $replyRoute  = 'coordinator.support.reply';
    $closeRoute  = 'coordinator.support.close';
    $createRoute = 'coordinator.support.create';
@endphp
@include('_support._show_content')
@endsection