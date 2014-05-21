@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.not-found.title'/*§:title - Page introuvable§*/, array('title' => §('global.title'/*§InSearch§*/))) }}</h1>

	<p>{{ §('global.not-found.description'/*§Il n'y a rien ici.§*/) }}</p>

@stop