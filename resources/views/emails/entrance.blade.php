@extends('beautymail::templates.widgets')

@section('content')

  @include('beautymail::templates.widgets.newfeatureStart')

    @php
      $code_style = '
                      display: inline-block;
                      background: #e6e6e6;
                      padding: .25em .4em .2em .4em;
                      border-radius: .5em;
                      color: black;
                      font-weight: bold;
                      font-family: "Consolas", "monospace", "Courier New", "Lucida Console", "Roboto Mono";
                      line-height: inherit;
      '
    @endphp


		<h4 class="secondary"><strong>Ihre Zugangsdaten</strong></h4>

    {{-- ID: {{ $id }}
    PAN: {{ $pan }}
    PIN: {{ $pin }} --}}

    <p>
      ID: {{ $user['id'] }}
      <br>

      PAN: <span style="{{ $code_style  }}"> {{ $user['pan']['pan'] }} </span>
      <br>

      PIN: <span style="{{ $code_style  }}">{{ $user['pan']['pin'] }}</span>
      <br>

      URL: <span><a href="{{ env('APP_URL') }}" target="_blank">{{ env('APP_URL') }}</a></span>
      <br>

    </p>


	@include('beautymail::templates.widgets.newfeatureEnd')

@stop
