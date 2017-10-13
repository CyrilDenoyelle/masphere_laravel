@extends('layouts.app')

@section('content')
<div class="container">
	<div class="pagecontainer row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading"><h3>Nouvel événement</h3></div>
				<form
				method="post"
				action="/event_update/{{$event->id}}"
				{{-- action="{{ route('event_update', {{$event->id}}) }}" --}}
				class="form panel-body">
					{{ csrf_field() }}
					<label title="Nommer l'événement" >Nom</label>
					<input required type="text" class="form-control" placeholder="nom de l'événement" aria-describedby="sizing-addon2" name="nom" value="{{ $event->nom }}">
					<br />
					{{-- {{ dd($event) }} --}}
					<label title="Utiliser l'autocomplétion pour avoir une adresse valide" >Lieu</label>
					<input
					required
					id="city"
					type="text"
					class="form-control"
					placeholder="lieu"
					aria-describedby="sizing-addon2"
					name="place"
					value="{{ $event->place }}"
					>
					<br />

					<label title="Date et heure" >Debut</label>
					<input required
					type="date"
					class="form-control"
					placeholder="jj/mm/aaaa"
					aria-describedby="sizing-addon2"
					name="debutDate"
					value="{{ date("Y-m-d", $event->debut) }}"
					>
					<input required
					type="text"
					class="form-control"
					placeholder="HH:mm"
					aria-describedby="sizing-addon2"
					name="debutHeure"
					value="{{ date("H:i", $event->debut) }}"
					>
					<br />

					<label title="Date et heure" >Fin</label>
					<input required
					type="date"
					class="form-control"
					placeholder="jj/mm/aaaa"
					aria-describedby="sizing-addon2"
					name="finDate"
					value="{{ date("Y-m-d", $event->fin) }}"
					>
					<input required
					type="text"
					class="form-control"
					placeholder="HH:mm"
					aria-describedby="sizing-addon2"
					name="finHeure"
					value="{{ date("H:i", $event->fin) }}"
					>
					<br />

					<label title="Url valide" >Lien vers la billetterie | facultatif</label>
					<input type="url"
					class="form-control"
					placeholder="http://example.com/billetterie"
					aria-describedby="sizing-addon2"
					name="billetterie"
					value="{{$event->billetterie}}" 
					>
					<br />
					{{-- 
					<label title="">Style musical | facultatif</label>
					<input type="text" class="form-control" placeholder="stylemusical" aria-describedby="sizing-addon2" name="stylemusical" value="">
					<br />
					 --}}
					<label title="">Commentaire | facultatif</label>
					<textarea type="text"
					class="form-control"
					placeholder="Commentaire"
					aria-describedby="sizing-addon2"
					name="textbox"
					value=""
					>{{$event->textbox}}</textarea>
					<br />

					<label title="">Spectacle | facultatif</label>
					<div class="perform_receiver">
						<input
						type="text"
						class="input-group input_group form-control"
						placeholder="nom du spectacle"
						aria-describedby="sizing-addon2"
						name="list_performs[0]"
						value="{{json_decode($event->list_performs)[0]}}"
						>

						@if(count(json_decode($event->list_performs)) > 1 )
						@php
						$old = json_decode($event->list_performs);
						$listTemp = array_shift($old);
						@endphp
						@foreach($old as $key => $perform)
						<div class="input-group input_group">
							<input
							type="text"
							class="input-group input_group form-control"
							placeholder="nom du spectacle"
							aria-describedby="sizing-addon2"
							name="list_performs[{{$key}}]"
							value="{{$old[$key]}}"
							>
							<span class="input-group-btn"><button type="button" class="input-group btn btn-danger suppr_perform_btn">suppr</button></span>
						</div>
						@endforeach
						@endif
					</div>
					<button type="button" class="input-group btn btn-basicfault add_perform_btn">ajouter un spectacle de plus</button>
					<br />

					<input type="submit" class="btn" value="Modifier l'événement">
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')

<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBV-FwOAdr1WGuFP1vhXI9fT4QMUvYiZnI&libraries=places&callback=initAutocomplete" async defer></script>

<script src="{{ asset('js/map.js') }}"></script>

<script src="{{ asset('js/event_creation.js') }}" type="text/javascript" ></script>

@endsection