@extends("modules.table.table")

@section("before-table")
    @if(current_user_can("view_appointments_list"))
        <div class="buttons-container">
            <a href="{{admin_url("admin.php?page=appointments-list")}}" class="button button-primary button-normal" style="margin-bottom: 1rem; margin-left: auto; display: block; width: fit-content">
                Prejsť na kalendár
            </a>
        </div>
    @endif
@endsection