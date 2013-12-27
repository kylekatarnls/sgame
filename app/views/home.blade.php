@extends('layouts.insearch')

@section('content')

	<h1>{{ _('global.title') }}</h1>

	@include('includes.searchbar', array('q' => ''))

@stop