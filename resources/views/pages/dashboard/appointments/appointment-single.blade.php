@php use Theme\Enum\AppointmentType; @endphp
@php use Theme\Enum\AppointmentStatus; @endphp

@extends('layouts.dashboard-layout')

@section('title')
    {{ __(($appointment->type === AppointmentType::RESERVATION ? "Rezervácia" : "Voľno") . " #{$appointment->id}", THEME_DOMAIN) }}
@endsection

@section('content')
    <div id="singleAppointment">

        <div id="single-data" data-delete_redirect="{{admin_url("admin.php?page=appointments-list")}}" data-resource='@json($appointment?->toArray())'></div>

        @include("parts.dashboard.appointments.modals.appointment-delete-modal")

        <div class="buttons-container" style="justify-content: flex-end">
            <div>Vytvorené: {{$appointment->created_at->format('d.m.y H:i')}}</div>
            <div>Posledná úprava: {{$appointment->updated_at->format('d.m.y H:i')}}</div>
            @if($appointment->status === AppointmentStatus::OK)
                <a href="{{$appointment->calendar_link}}" class="button button-primary button-large">Upraviť v kalendári</a>
                <button class="button button-danger button-large" @click="visibleDeleteModal = true">Zrušiť termín</button>
            @endif
        </div>

        <div class="row">
            @if($appointment->type === AppointmentType::RESERVATION)
                <div class="third">
                    <h3>Informácie</h3>
                    <div class="info-card">
                        <div class="info-card-row">
                            <div class="label">Zákazník</div>
                            <div class="value"><a
                                        href="{{$appointment->customer->edit_link}}">{{$appointment->customer->title}}</a>
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Pracovník</div>
                            <div class="value"><a
                                        href="{{$appointment->employee->edit_link}}">{{$appointment->employee->display_name}}</a>
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Dátum</div>
                            <div class="value">
                                <div class="date-with-break-row">
                                    {{$appointment->date_string}}
                                    <br>
                                    <small>s prestávkou do {{$appointment->end_at_with_break->format('H:i')}}
                                        ({{$appointment->break}}min)</small>
                                </div>
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Suma</div>
                            <div class="value">{!! $appointment->formatted_total !!}</div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Služby</div>
                            <div class="value">{!! $appointment->formatted_services !!}</div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Poznámka</div>
                            <div class="value">
                                @if(!empty($appointment->note))
                                    {{ $appointment->note }}
                                @else
                                    <i>Bez poznámky</i>
                                @endif
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Objednaný cez</div>
                            <div class="value">{{ $appointment->source->translated() ?? "Nezadané" }}</div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Status</div>
                            <div class="value">{{ $appointment->status->translated() ?? "Nezadané" }}</div>
                        </div>
                    </div>
                </div>
                <div class="third">
                    <h3>Platby</h3>
                    @if($appointment->payments->count())
                        <table class="info-table">
                            <thead>
                            <tr>
                                <th>Suma</th>
                                <th>Spôsob</th>
                                <th>Poznámka</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($appointment->payments as $payment)
                                <tr>
                                    <td>{{$payment->amount}}€</td>
                                    <td>{{$payment->type->translated()}}</td>
                                    <td>{{$payment->note}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Žiadne platby.</p>
                    @endif
                </div>
                <div class="third">
                    <h3>Predané produkty</h3>
                    @if($appointment->productSales->count())
                        <table class="info-table">
                            <thead>
                            <tr>
                                <th>Produkt</th>
                                <th>Počet</th>
                                <th>Celkom (€)</th>
                                <th>Spôsob</th>
                                <th>Poznámka</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($appointment->productSales as $payment)
                                <tr>
                                    <td>{{$payment->product->title}}</td>
                                    <td>{{$payment->quantity}}</td>
                                    <td>{{$payment->total_price}}€</td>
                                    <td>{{$payment->payment_type?->translated() ?? "Nezadané"}}</td>
                                    <td>{{$payment->note}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Žiadne predané produkty.</p>
                    @endif
                </div>
            @else
                <div class="third">
                    <h3>Informácie</h3>
                    <div class="info-card">
                        <div class="info-card-row">
                            <div class="label">Pracovník</div>
                            <div class="value"><a
                                        href="{{$appointment->employee->edit_link}}">{{$appointment->employee->display_name}}</a>
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Dátum</div>
                            <div class="value">
                                <div class="date-with-break-row">
                                    {{$appointment->date_string}}
                                </div>
                            </div>
                        </div>
                        <div class="info-card-row">
                            <div class="label">Poznámka</div>
                            <div class="value">
                                @if(!empty($appointment->note))
                                    {{ $appointment->note }}
                                @else
                                    <i>Bez poznámky</i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection