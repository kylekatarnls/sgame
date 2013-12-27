@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.title') }}</h1>

	@include('includes.searchbar', array('q' => ''))

@stop