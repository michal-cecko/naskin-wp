@php
    $facebook = $facebook ?? get_field("socials_facebook", "options");
    $instagram = $instagram ?? get_field("socials_instagram", "options");
    $web = $web ?? home_url();
@endphp

<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
			padding-top: 25px;" class="social-icons">
        <table width="256" border="0" cellpadding="0" cellspacing="0" align="center"
               style="border-collapse: collapse; border-spacing: 0; padding: 0;">
            <tr>

                <!-- WEB -->
                <td align="center" valign="middle"
                    style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;">
                    <a target="_blank" href="{{ $web }}" style="text-decoration: none;">
                        <img border="0" vspace="0" hspace="0" width="44" height="44" alt="WEB" style="padding: 0; margin: 0; outline: none;
                                        text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block; color: #000000;"
                             src="{{main()->assets()->static("images/web.php")}} . ">
                    </a>
                </td>

                <!-- FACEBOOK START -->
                @if(!empty($facebook))
                    <td align="center" valign="middle"
                        style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;">
                        <a target="_blank" href="{{$instagram}}" style="text-decoration: none;">
                            <img border="0" vspace="0" hspace="0" width="44" height="44" alt="FB" style="padding: 0; margin: 0; outline: none;
                                        text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block; color: #000000;"
                                 src="{{main()->assets()->static("images/facebook.php")}}">
                        </a>
                    </td>
                @endif
                <!-- FACEBOOK END -->

                @if(!empty($instagram))
                    <!-- INSTAGRAM -->
                    <td align="center" valign="middle"
                        style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;">
                        <a target="_blank" href="{{$instagram}}"
                           style="text-decoration: none;">
                            <img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="IG" title="Instagram"
                                 width="44" height="44"
                                 src="{{main()->assets()->static("images/instagram.php")}}">
                        </a>
                    </td>
                @endif
            </tr>
        </table>
    </td>
</tr>