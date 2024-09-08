<h4>Platby</h4>
@if(!empty($appointment->payments->isNotEmpty()))
    @foreach($appointment->payments as $payment)
        <div class="row">
            <div class="fifth">
                {{ $payment->amount }}€
            </div>
            <div class="fifth">
                @if($payment->type)
                    {{ $payment->type->translated() }}
                @else
                    <i>Nezadaný spôsob platby</i>
                @endif
            </div>
            @if(!empty($payment?->note))
                <div class="sixty">
                    Poznámka: {{ $payment?->note }}
                </div>
            @endif
        </div>
    @endforeach
@else
    <div class="row">
        <div class="full">Žiadne evidované platby.</div>
    </div>
@endif
<br>
<h4>Predaje</h4>
@if(!empty($appointment->productSales->isNotEmpty()))
    @foreach($appointment->productSales as $sale)
        <div class="row">
            <div class="fifth">
                <a href="{{$sale->product->edit_link}}"><strong>{{ $sale->product->title }}</strong></a>
            </div>
            <div class="fifth">
                <strong>{{ $sale->quantity }} x {{ $sale->price }}€ = {{$sale->total_price}}€ - {!! $sale->payment_type?->translated() ?? "<i>Nezadaný spôsob platby</i>" !!}</strong>
            </div>
            <div class="fifth">
                <strong>dňa {{ $sale->sold_at->format("d.m.y H:i") }}</strong>
            </div>
            @if(!empty($sale->note))
                <div class="forty">
                    Poznámka: <strong>{{ $sale->note }}</strong>
                </div>
            @endif
        </div>
    @endforeach
@else
    <div class="row">
        <div class="full">Žiadne evidované predaje produktov.</div>
    </div>
@endif
