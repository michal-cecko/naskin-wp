@extends("modules.table.table")

@section("before-table")
    @if(current_user_can("create_expenses"))
        <div class="buttons-container">
            <a href="{{admin_url("admin.php?page=expense_detail")}}" class="button button-primary button-normal" style="margin-bottom: 1rem; margin-left: auto; display: block; width: fit-content">
                Pridať výdavok
            </a>
        </div>
    @endif
@endsection