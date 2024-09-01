@extends('layouts.dashboard-layout')

@section('title')
    {{ __($resource ? "Detail predaja produktu #{$resource->id}" : 'Pridať predaj produktu', THEME_DOMAIN) }}
@endsection

@section('content')

    <div id="singleProductSale">

        <div id="single-data" data-form_type="{{$resource ? "edit" : "create"}}" data-delete_redirect="{{admin_url("admin.php?page=product_sales")}}"
             data-resource='@json($resource?->toArray())' data-reservations='@json($reservations)' data-products='@json($products)'></div>

        <div class="single-form">
            <div class="divided-row">
                <div class="half">
                    <div class="field-container">
                        <label for="product_id">Produkt</label>
                        <p-select v-model="form.product_id" :options="products" option-label="title" option-value="id" filter @change="setPriceIfEmpty()" placeholder="Vyberte produkt"></p-select>
                    </div>
                </div>
                <div class="half">
                    <div class="field-container">
                        <label for="appointment_id">Rezervácia</label>
                        <p-select v-model="form.appointment_id" :options="reservations" option-label="title" option-value="id" filter placeholder="Vyberte rezerváciu" show-clear></p-select>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="price">Suma (€)</label>
                        <p-number v-model="form.price" mode="currency" currency="EUR"
                                  placeholder="Suma (€)" locale="sk-SK"></p-number>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="quantity">Množstvo</label>
                        <p-number v-model="form.quantity" :min="0.01" show-buttons :step="0.01" :maxFractionDigits="2"  placeholder="Množstvo"></p-number>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="sold_at">Dátum predania</label>
                        <p-datepicker v-model="form.sold_at" date-format="dd/mm/yy" show-button-bar placeholder="Vyberte dátum"></p-datepicker>
                    </div>
                </div>
                <div class="full">
                    <div class="field-container">
                        <label for="note">Poznámka</label>
                        <input class="custom-text-input" type="text" v-model="form.note" placeholder="Poznámka...">
                    </div>
                </div>
            </div>
            <div class="buttons-container">
                @if($resource && current_user_can('delete_product_sales'))
                    <button type="button" class="button button-danger button-normal" :class="deleteLoader ? 'button-loading' : ''" @click="removeProductSale()" style="margin-top: 1rem">
                        @{{buttonRemoveText}}
                    </button>
                @endif
                <button type="button" class="button button-primary button-normal" :class="createEditLoader ? 'button-loading' : ''" @click="saveProductSale()" style="margin-top: 1rem; margin-left: auto; display: block; width: fit-content">
                    @{{buttonSaveText}}
                </button>
            </div>
        </div>
    </div>

@endsection