@extends('layouts.main')

@section('title', $event->title)

@section('content')

<div class="col-md-10 offset-md-1">
    <div class="row">
        <div id="image-container" class="col-md-6">
            <img src="/img/events/{{ $event->image }}" class="img-fluid" alt="{{ $event->title }}">
        </div>
        <div id="info-container" class="col-md-6">
            <h1>{{ $event->title }}</h1>
            <p class="event-city"> {{ $event->city }}</p>
            <p class="event-participants">{{ count($event->users) }} Participantes</p>
            <p class="event-owner">{{ $eventOwner['name'] }}</p>
            @if(!$hasUserJoined)
                <form action="/events/joins/{{ $event->id }}" method="POST">
                    @csrf
                    <a href="/events/joins/{{ $event->id }}"
                    class="btn btn-primary" 
                    id="event-submit"
                    onclick="event.preventDefault();
                    this.closest('form').submit();">
                    Confirmar Presenca
                    </a>
                </form>
            @else
                <p class="already-joined-msg">Voce ja esta participando do evento</p>
            @endif

            
            <h3>O evento conta com:</h3>
            <ul id="items-list">
            @if(is_array($event->items))
                @foreach($event->items as $item)
                    <li> <span> {{ $item }} </span></li>
                @endforeach
            @endif
            </ul>
        </div>
        <div class="col-md-12" id="description-container">
            <h3>Sobre o evento</h3>
            <p class="event-description">{{ $event->description }}</p>
        </div>
    </div>
</div>

@endsection