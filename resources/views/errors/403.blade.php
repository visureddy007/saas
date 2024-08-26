@extends('errors::minimal')

@section('title', __tr('Forbidden'))
@section('code', '403')
@section('message', __tr($exception->getMessage() ?: 'Forbidden'))
