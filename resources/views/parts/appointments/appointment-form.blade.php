@php
    use Theme\Services\Services\ServiceCategoryService;
@endphp

<div id="reservation" data-categories='@json(ServiceCategoryService::getServiceCategories())'>
    <div class="container">
        <div class="reservation-wrapper">
            <div class="reservation-container">
                <div class="header" :class="step > 1 || !!chosenCategory ? 'hasBackButton' : ''">
                    <div class="header-text">
                        <div class="back-button" @click="changeStep(step - 1, true)">
                            {!! main()->assets()->svg("icons/reservation/icon-arrow.svg") !!}
                        </div>
                        <div class="texts">
                            <span v-if="step === 1">Výber služieb</span>
                            <span v-else-if="step === 2">Výber pracovníčky</span>
                            <span v-else-if="step === 3">Výber dátumu</span>
                            <span v-else-if="step === 4">Výber času</span>
                            <span v-else>Kontaktné údaje</span>
                        </div>
                    </div>
                    <div class="close" data-js-toggle-reservation-modal>
                        {!! main()->assets()->svg("icons/reservation/icon-close.svg") !!}
                    </div>
                </div>
                <div class="content-container">
                    <div class="content choose-service" :class="chosenCategory ? 'shown-services' : ''">
                        <div class="categories-list">
                            <div class="category" :class="hasChosenCategoryClass()"
                                 v-for="category in categories" :key="category.id"
                                 @click="chooseCategory(category)">
                                <div class="name">@{{category.name}}</div>
                                <div class="image" v-if="category.image">
                                    <img :src="category.image" :alt="category.name">
                                </div>
                            </div>
                        </div>
                        <div class="services-list">
                            <div class="services-grid">
                                <div class="service"
                                     :class="{ chosen: !!chosenServices[service.id] }"
                                     v-for="service in availableServices" :key="service.id"
                                     @click="toggleService(service)">
                                    <div class="name">@{{service.title}}</div>
                                    <div class="duration">@{{service.duration}} min</div>
                                    <div class="price">@{{service.price}} €</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content choose-employee">
                        <div class="employee" @click="chooseEmployee(-1)" :class="activeEmployeeClass(-1)"
                             v-if="availableEmployees.length > 1">
                            <div class="img-container any">
                                {!! main()->assets()->svg("icons/reservation/icon-random.svg") !!}
                            </div>
                            <div class="name">Nezáleží</div>
                        </div>
                        <div class="employee" :class="chosenEmployee?.id === employee.id ? 'chosen' : ''"
                             v-for="employee in availableEmployees" @click="chooseEmployee(employee.id)"
                             :key="employee.id">
                            <div class="img-container">
                                <img v-if="!!employee.profile_picture" :src="employee.profile_picture"
                                     :alt="employee.first_name">
                                <template v-else>
                                    {!! main()->assets()->svg("icons/reservation/icon-question_mark.svg") !!}
                                </template>
                            </div>
                            <div class="name">@{{employee.first_name}}</div>
                        </div>
                    </div>
                    <div class="content choose-date position-relative">
                        <div class="date-picker" :class="Object.keys(availableDates).length ? 'shown' : ''">
                            <div v-for="(dates, month, index) in availableDates" class="dates-container">
                                <h3 class="month-name" v-html="getMonthName(month)"></h3>
                                <div class="date-grid">
                                    <template v-for="(appointments, availableDate, index) in dates">
                                        <div class="date"
                                             :class="[availableDate === date ? 'chosen' : '', appointments['isAvailable'] === 0 ? 'notAvailable' : '']"
                                             @click="chooseDate(availableDate)">
                                            <div class="number" v-html="getMomentDate(availableDate, 'D')"></div>
                                            <div class="name"
                                                 v-html="getDayName(getMomentDate(availableDate, 'd'))"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="empty-date-picker" :class="Object.keys(availableDates).length ? '' : 'shown'">
                            <lord-icon
                                    src="{{main()->assets()->static("icons/reservation/icon-loader.json")}}"
                                    trigger="loop"
                                    stroke="60"
                                    colors="primary:#D3932A,secondary:#D3932A">
                            </lord-icon>
                        </div>
                    </div>
                    <div class="content choose-time position-relative">
                        <div class="time-picker" v-if="Object.keys(timeOptions).length">
                            <div v-for="(timeArray, availableTime, index) in timeOptions" class="time-container"
                                 :class="[!empty(chosenTime.time) && availableTime === chosenTime.time ? 'chosen' : '', (timeArray.isAvailable === 0 ? 'unavailable' : '')]"
                                 @click="chooseTime(availableTime)">
                                <div class="time" :class="getTimeClass(availableTime)"
                                     v-html="getMomentDate('2022-03-21T' + availableTime + ':00', 'H:mm')"></div>
                            </div>
                        </div>
                    </div>
                    <div class="content choose-contact position-relative">
                        <div class="input--text input--light_beige input--normal"
                             :class="hasError('name') ? 'error' : ''">
                            <label>Meno a priezvisko</label>
                            <input type="text" v-model="customer.name">
                        </div>
                        <div class="input--text input--light_beige input--normal"
                             :class="hasError('email') ? 'error' : ''">
                            <label>Email</label>
                            <input type="text" v-model="customer.email">
                        </div>
                        <div class="input--text input--light_beige input--normal"
                             :class="hasError('phone') ? 'error' : ''">
                            <label>Telefón</label>
                            <input type="text" v-model="customer.phone">
                        </div>
                        <div class="input--text input--light_beige input--normal">
                            <label>Poznámka</label>
                            <input type="text" v-model="customer.note">
                        </div>
                        <label for="saveCustomerToCookies" class="form-checkbox label-right">
                            <input type="checkbox" v-model="saveCustomerToCookies" id="saveCustomerToCookies">
                            <span class="checkbox">
                                {!! main()->assets()->svg("icons/reservation/icon-check.svg") !!}
                            </span>
                            <span class="form-label">Uložiť údaje</span>
                        </label>
                    </div>
                </div>
                <div id="current-reservation"
                     :class="[(isVisibleOrder ? 'visible' : ''), (step === 5 ? 'bigger' : '')]">
                    <div class="header" @click="headerToggler()">
                        <span class="header-text">Súhrn rezervácie</span>
                        <span class="toggle-button" :class="step !== 5 ? 'active' : ''">
                            {!! main()->assets()->svg("icons/reservation/icon-arrow.svg") !!}
                        </span>
                    </div>
                    <div class="order" v-if="hasChosenServices()">
                        {{--<div class="img-container">
                            <template v-if="chosenEmployee?.profile_picture">
                                <img :src="chosenEmployee.profile_picture" :alt="chosenEmployee.first_name">
                            </template>
                            <template v-else-if="chosenEmployee?.id === -1">
                                {!! main()->assets()->svg("icons/reservation/icon-random.svg") !!}
                            </template>
                            <template v-else>
                                {!! main()->assets()->svg("icons/reservation/icon-question_mark.svg", ['class'=> ['unknown_icon']]) !!}
                            </template>
                        </div>--}}
                        <div class="info">
                            <div class="name-price-container">
                                <div class="name">@{{ chosenEmployee?.first_name ?? "Pracovníčka" }}</div>
                                <div class="summed-duration-price">
                                    <span class="duration">
                                         @{{ totalDuration }}
                                    </span>
                                    <span class="price">
                                         @{{ totalPrice }}€
                                    </span>
                                </div>
                            </div>
                            <div class="services">
                                <div v-show="service.id !== null" class="service" v-for="service in chosenServices"
                                     :key="service.id">
                                    @{{ service.title }}
                                    <div class="service-duration-price">
                                        <span class="duration">
                                            @{{ formatDuration(service.duration) }}
                                        <span class="price">
                                            @{{ service.price }}€
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-show="date !== null" class="date">@{{ formatSelectedDatetime() }}</div>
                        </div>
                    </div>
                    <div class="contact-data" v-if="step === 5">
                        <div class="label" v-if="!hasError(customer.name)">Meno:</div>
                        <div class="value" v-if="!hasError(customer.name)">@{{ customer.name }}</div>
                        <div class="label" v-if="!hasError(customer.email)">Email:</div>
                        <div class="value" v-if="!hasError(customer.email)">@{{ customer.email }}</div>
                        <div class="label" v-if="!hasError(customer.phone)">Telefón:</div>
                        <div class="value" v-if="!hasError(customer.phone)">@{{ customer.phone }}</div>
                    </div>
                    <div class="button-container">
                        <button class="btn btn--brownish_yellow next-step"
                                :class="!canContinue(2) ? 'btn-disabled' : ''" :disabled="!canContinue(2)"
                                v-if="step === 1" @click="changeStep(2)">Vybrať dátum
                        </button>
                        <button class="btn btn--brownish_yellow next-step"
                                :class="!canContinue(3) ? 'btn-disabled' : ''" :disabled="!canContinue(3)"
                                v-else-if="step === 2" @click="changeStep(3)">Vybrať dátum
                        </button>
                        <button class="btn btn--brownish_yellow next-step"
                                :class="!canContinue(4) ? 'btn-disabled' : ''" :disabled="!canContinue(4)"
                                v-else-if="step === 3" @click="changeStep(4)">Vybrať čas
                        </button>
                        <button class="btn btn--brownish_yellow next-step"
                                :class="!canContinue(4) ? 'btn-disabled' : ''" :disabled="!canContinue(5)"
                                v-else-if="step === 4" @click="changeStep(5)">Kontaktné údaje
                        </button>
                        <button class="btn btn--brownish_yellow next-step" v-else
                                @click="makeReservation()">
                            Rezervovať
                        </button>
                    </div>
                </div>
                <div class="sending-overlay"
                     :class="[(sending ? 'sending' : ''), (sent ? 'sent' : ''), (responseError ? 'errored' : '')]">
                    <div class="icon-wrapper">
                        <lord-icon
                                src="{{main()->assets()->static("icons/reservation/icon-loader.json")}}"
                                trigger="loop"
                                stroke="60"
                                class="loader"
                                colors="primary:#D3932A,secondary:#D3932A">
                        </lord-icon>
                        <lord-icon
                                src="{{main()->assets()->static("icons/reservation/icon-check.json")}}"
                                trigger="click"
                                stroke="100"
                                class="check"
                                colors="primary:#D3932A,secondary:#D3932A">
                        </lord-icon>
                        <lord-icon
                                src="{{main()->assets()->static("icons/reservation/icon-error.json")}}"
                                trigger="click"
                                stroke="100"
                                class="errorCross"
                                colors="primary:#c92727,secondary:#c92727">
                        </lord-icon>
                    </div>
                    <span class="response-message"></span>
                </div>
            </div>
        </div>
    </div>
</div>