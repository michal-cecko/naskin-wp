@extends('layouts.dashboard-layout')

@section('title')
    {{ __($resource ? "Detail výdavku #{$resource->id}" : 'Pridať výdavok', THEME_DOMAIN) }}
@endsection

@section('content')

    <div id="singleExpense">

        <div id="single-data" data-form_type="{{$resource ? "edit" : "create"}}" data-delete_redirect="{{admin_url("admin.php?page=expenses")}}"
             data-resource='@json($resource?->toArray())' data-categories='@json($categories)' data-products='@json($products)'></div>

        <div class="single-form">
            <div class="divided-row">
                <div class="third">
                    <div class="field-container">
                        <label for="description">Popis</label>
                        <input class="custom-text-input" type="text" v-model="form.description" placeholder="Popis...">
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="category_id">Kategória</label>
                        <p-select v-model="form.category_id" :options="categories" option-label="title"
                                  option-value="id" filter placeholder="Vyberte kategóriu" show-clear></p-select>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="category_id">Produkt</label>
                        <p-select v-model="form.product_id" :options="products" option-label="title" show-clear
                                  option-value="id" filter placeholder="Vyberte produkt"></p-select>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="price">Cena (€)</label>
                        <p-number v-model="form.price" mode="currency" currency="EUR"
                                  placeholder="Cena (€)" locale="sk-SK"></p-number>
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="supplier">Dodávateľ</label>
                        <input class="custom-text-input" type="text" v-model="form.supplier" placeholder="Dodávateľ...">
                    </div>
                </div>
                <div class="third">
                    <div class="field-container">
                        <label for="bought_at">Z dňa</label>
                        <p-datepicker v-model="form.bought_at" date-format="dd/mm/yy" show-button-bar placeholder="Vyberte dátum"></p-datepicker>
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
                @if($resource && current_user_can('delete_expenses'))
                    <button type="button" class="button button-danger button-normal" :class="deleteLoader ? 'button-loading' : ''" @click="removeExpense()" style="margin-top: 1rem">
                        @{{buttonRemoveText}}
                    </button>
                @endif
                <button type="button" class="button button-primary button-normal" :class="createEditLoader ? 'button-loading' : ''" @click="saveExpense()" style="margin-top: 1rem; margin-left: auto; display: block; width: fit-content">
                    @{{buttonSaveText}}
                </button>
            </div>
        </div>
    </div>

@endsection