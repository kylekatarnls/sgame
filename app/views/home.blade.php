@extends('layouts.insearch')

@section('content')

	<h1>InSearch</h1>

	@include('includes.searchbar', array('q' => ''))

@stop