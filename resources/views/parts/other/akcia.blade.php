@if(!empty($imgSrc = get_field("akcia_letak", "options")))
    <div class="custom-dialog-wrapper" id="akciaDialog">
        <div class="backdrop"></div>
        <div class="custom-dialog">
            <div class="custom-dialog--header">
                <h5 class="custom-dialog--header--title">Akcia</h5>
                <button type="button" class="close"></button>
            </div>
            <div class="custom-dialog--body">
                <img src="{{ $imgSrc }}" alt="Aktuálna akcia">
            </div>
        </div>
    </div>
@endif