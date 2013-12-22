@extends('layouts.insearch')

@section('content')

	<h1>InSearch</h1>

	@include('includes.searchbar', ['q' => ''])

@stop