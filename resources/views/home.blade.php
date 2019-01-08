@extends('layouts.app')

@section('content')

    <home
        now="{{ $now }}"
        :num-listings="{{ $numListings }}"
        :num-agents={{ $numAgents }}
    ></home>

@endsection
