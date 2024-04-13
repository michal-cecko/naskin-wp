<?php
add_action('wp_ajax_send_form_email', 'contact_form');
add_action('wp_ajax_nopriv_send_form_email', 'contact_form');

function getEmailTemplate($emailType, $data)
{
    ob_start();
    get_template_part("template_parts/email_template", "", ['type' => $emailType, 'data' => $data]);
    return ob_get_clean();
}


function reservation_notification($to, $type, $id, $sendingToEmployee = false)
{
    $service = get_field("appointment_service", $id);
    $employee = get_field("appointment_employee", $id);
    $serviceTitle = get_the_title($service->ID);

    $message = getEmailTemplate($type, [
        'employee' => $employee,
        'service' => $service,
        "id" => $id,
        "sendingToEmployee" => $sendingToEmployee
    ]);

    if ($type === "new") {
        $subject = 'Nová rezervácia | ' . $serviceTitle;
    } else if ($type === "update") {
        $subject = 'Úprava vašej rezervácie | ' . $serviceTitle;
    } else if ($type === "notification") {
        $subject = 'Pripomienka rezervácie | ' . $serviceTitle;
    } else { // cancel
        $subject = 'Zrušenie termínu | ' . $serviceTitle;
    }

    //TODO doplniť replyto a from adresu
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: naskin.sk <' . $to . '>',
        'Reply-to: info@naskin.sk'
    ];

    return wp_mail($to, $subject, $message, $headers);
}