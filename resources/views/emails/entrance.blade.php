@extends('beautymail::templates.widgets')

@section('content')

	@include('beautymail::templates.widgets.articleStart')

		<h4 class="secondary"><strong>Hello World</strong></h4>
    <p>This is a test</p>

    {{-- ID: {{ $id }}
    PAN: {{ $pan }}
    PIN: {{ $pin }} --}}

    ID: {{ $user['id'] }} <br>
    PAN: {{ $user['pan']['pan'] }} <br>
    PIN: {{ $user['pan']['pin'] }} <br>

	@include('beautymail::templates.widgets.articleEnd')


	@include('beautymail::templates.widgets.newfeatureStart')

		<h4 class="secondary"><strong>Hello World again</strong></h4>
		<p>This is another test</p>

	@include('beautymail::templates.widgets.newfeatureEnd')

@stop
