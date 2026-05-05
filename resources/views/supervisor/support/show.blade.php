@extends('layouts.supervisor-app')
@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)
@section('content')
@php
    $indexRoute  = 'supervisor.support.index';
    $replyRoute  = 'supervisor.support.reply';
    $closeRoute  = 'supervisor.support.close';
    $createRoute = 'supervisor.support.create';
@endphp
@include('_support._show_content')
@endsection