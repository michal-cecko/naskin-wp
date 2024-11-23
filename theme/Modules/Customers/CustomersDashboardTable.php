<?php

namespace Theme\Modules\Customers;

use Carbon\Carbon;
use Exception;
use Theme\Mail\DefaultEmail;
use Theme\PostTypes\Customer;

class CustomersDashboardTable
{
    /**
     * @filter bulk_actions-edit-customer
     *
     * @return array
     */
    public function addSendBulkEmailAction(): array
    {
        $bulk_actions['send-bulk-email'] = __('Odoslať hromadný email', THEME_DOMAIN);
        return $bulk_actions;
    }

    /**
     * @action admin_footer
     *
     * @return void
     */
    public function addEmailModalHTMLToAdminFooter(): void
    {
        if (get_current_screen()->post_type !== Customer::getPostTypeSlug()) return;

        wp_print_media_templates();

        templates()->render("parts.dashboard.other.send-bulk-email-modal");
    }

    /**
     * @action wp_ajax_send_bulk_message
     *
     * @return void
     * @throws Exception
     */
    public function ajaxMessageHandler() : void {
        check_ajax_referer('send-bulk-message-nonce', 'security');

        $post_ids = $_POST['post_ids'];
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']);

        $success = collect();
        $fail = collect();

        $customers = Customer::whereIn('ID', $post_ids)->get();

        $mailable = new DefaultEmail($subject, $message);

        foreach ($customers as $customer) {
            if ($customer->email) {
                $sent = main()->mail()->send($mailable, $customer->email);
                if ($sent) {
                    $success->push($customer);
                } else {
                    $fail->push($customer);
                }
            }
        }

        main()->log()->infoDB("Odoslaný hromadný email zákazníkom {$success->pluck("title")->implode(", ")}. Predmet správy: {$subject}. Obsah správy: {$message}. Odoslaných: {$success->count()}. Neodoslaných: {$fail->count()} ({$fail->pluck("title")->implode(", ")}).");

        wp_send_json_success([
            'message' => sprintf(
                'Odoslanie emailov dokončené. Úspešne odoslaných: %d, Nepodarilo sa odoslať: %d. Záznam nájdete v logu.',
                $success->count(),
                $fail->count()
            )
        ]);
    }

    /**
     * Ensure WordPress editor scripts are loaded
     * @action admin_enqueue_scripts
     *
     * @return void
     */
    public function enqueueWpEditorScriptsForEmailSending() : void {
        $screen = get_current_screen();
        if ($screen->post_type === Customer::getPostTypeSlug()) {
            wp_enqueue_editor();
            wp_enqueue_media();

            // Add WordPress default styles and scripts needed for media modal
            wp_enqueue_style('wp-admin');
            wp_enqueue_style('wp-mediaelement');
            wp_enqueue_style('media-views');

            wp_enqueue_script('wp-mediaelement');
            wp_enqueue_script('media-editor');
            wp_enqueue_script('media-views');
            wp_enqueue_script('media-grid');
            wp_enqueue_script('media');
        }
    }

    /**
     * Adds custom columns to the customers table
     *
     * @filter manage_customer_posts_columns
     */
    public function custom_customers_columns($columns)
    {
        $dateColumn = $columns['date'];

        unset($columns['date']);

        $columns['email'] = 'Email';
        $columns['phone'] = 'Telefón';
        $columns['last_appointment'] = 'Dátum posledného termínu';
        $columns['date'] = $dateColumn;

        return $columns;
    }

    /**
     * Populates custom columns in the customers table
     *
     * @action manage_customer_posts_custom_column
     */
    public function custom_customers_column_data($column, $post_id): void
    {
        switch ($column) {
            case 'email':
                $email = get_field('cust_email', $post_id);
                echo !empty($email) ? $email : "-";
                break;
            case 'phone':
                $phone = get_field('cust_phone', $post_id);
                echo !empty($phone) ? $phone : "-";
                break;
            case 'last_appointment':
                $date = get_field('cust_last-appointment', $post_id);
                echo !empty($date) ? Carbon::parse($date)->format("j.n.Y") : "-";
                break;
        }
    }

    /**
     * Hides title, slug and permalink for customers, we dont need them.
     *
     * @action edit_form_after_title
     * @return void
     */
    public function hide_title_slug_permalink_for_customers(): void
    {
        global $post;

        if (Customer::getPostTypeSlug() === $post->post_type) :?>
            <style type="text/css">
                #edit-slug-box,
                #sample-permalink,
                #slugdiv
                    /*#post-body-content*/
                {
                    display: none !important;
                }
            </style>
        <?php endif;
    }
}