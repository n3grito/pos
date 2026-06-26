@extends('errors.minimal')

@section('title', '403')
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Acceso denegado'))
