@extends('errors::minimal')

@section('title', ws___('Forbidden'))
@section('code', '403')
@section('message', ws___($exception->getMessage() ?: 'Forbidden'))
