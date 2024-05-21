<div id="customNotifications" class="custom-notifications"></div>

<div id="calendarContainer">
    <input type="hidden" id="page-data" data-colors='@json($services['colors'])' data-services='@json($services['services'])'
           data-durations='@json($services['durations'])' data-employees='@json($employees)'>

    <input type="hidden" id="logged-user" data-id="{{$currentUser->id}}"
         data-name="{{$currentUser->first_name}}" data-role="{{$currentUser->role}}">

    <div class="header-wrapper mb-3 d-flex align-items-center justify-content-between flex-wrap">
        @if($currentUser->role !== "employee")
            <div class="employees-toggler">
                <div class="employee" @click="changeCurrentEmployeeView(-1)"
                     :class="chosenEmployeeOnView === -1 ? 'active' : ''">
                    <div class="img">
                        {!! main()->assets()->svg("icons/admin/icon-all_employees.svg") !!}
                    </div>
                    <span>Všetci</span>
                </div>
                @foreach($employees as $employee)
                    <div class="employee" @click="changeCurrentEmployeeView({{ $employee['id'] }})"
                         :class="chosenEmployeeOnView === {{ $employee['id'] }} ? 'active' : ''">
                        <div class="img">
                            @if(!empty($employee['profileImage']))
                                <img src="{{$employee['profileImage']}}" alt="Fotka">
                            @else
                                {!! main()->assets()->svg("icons/admin/icon-question_mark_admin.svg") !!}
                            @endif
                        </div>
                        <span>{{$employee['name']}}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div></div>
        @endif

        <div class="buttons-wrapper ml-auto d-flex align-items-center flex-wrap">
            <button type="button" class="btn btn-primary" @click="createModal.show()">Pridať termín</button>
        </div>

    </div>

    <div id="calendar"></div>

    @include("parts.dashboard.appointments.modals.appointment-create-modal", ['services' => $services['service_categories']])

    @include("parts.dashboard.appointments.modals.appointment-edit-modal", ['services' => $services['service_categories']])

    @include("parts.dashboard.appointments.modals.appointment-delete-modal")
</div>







<style>
    #wpbody-content .wrap {
        display: none;
    }
</style>