Dobry den, pripominame, ze sa blizi datum Vasej rezervacie:

Datum a cas: {{ $appointment->start_at->format("d.m.Y, H:i") }}
@if(!empty($miesto = get_field("address", "options")))
Miesto: {!! $miesto !!}
@endif
Sluzby: {{ main()->wpHelper()->removeAccentsFromString($appointment->services->map(fn($service) => $service->name)->implode(", ")) }}