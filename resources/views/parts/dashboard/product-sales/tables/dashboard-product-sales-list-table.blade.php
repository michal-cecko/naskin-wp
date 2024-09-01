@extends("modules.table.table")

@section("before-table")
    @if(current_user_can("create_product_sales"))
        <div class="buttons-container">
            <a href="{{admin_url("admin.php?page=product_sale_detail")}}" class="button button-primary button-normal" style="margin-bottom: 1rem; margin-left: auto; display: block; width: fit-content">
                Pridať predaj
            </a>
        </div>
    @endif
@endsection