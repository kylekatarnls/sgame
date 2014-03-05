@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.not-found.title', array('title' => §('global.title'))) }}</h1>

	<p>{{ §('global.not-found.description') }}</p>

@stop