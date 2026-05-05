@extends('layouts.app')
@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)
@section('content')
@php
    $indexRoute  = 'admin.support.index';
    $replyRoute  = 'admin.support.reply';
    $closeRoute  = 'admin.support.close';
    $createRoute = 'admin.support.create';
@endphp
@include('_support._show_content')
@endsection