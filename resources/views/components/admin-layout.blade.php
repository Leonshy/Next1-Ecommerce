@props(['title' => 'Admin'])

<x-slot name="title">{{ $title }}</x-slot>

@include('layouts.admin', ['title' => $title])
