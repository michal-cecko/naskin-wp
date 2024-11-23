<div id="send-bulk-message-modal" class="custom-dialog-wrapper">

    <div class="backdrop"></div>

    <div class="custom-dialog">

        <div class="custom-dialog--header ">
            <h3 class="custom-dialog--header--title">Hromadný email</h3>
            <button type="button" id="close-modal" class="close"></button>
        </div>

        <div class="custom-dialog--body">
            <div class="field-container">
                <label for="message-subject">Predmet emailu</label>
                <input type="text" class="custom-text-input" id="message-subject" style="width:100%;margin:10px 0;">
            </div>
            <div class="field-container">
                <label for="message-content">Správa</label>
                <?php wp_editor('', 'message-content', array(
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                    'teeny' => false,
                    'quicktags' => true,
                    'tinymce' => array(
                        'height' => 300
                    )
                )); ?>
            </div>
            <div id="selected-customers-info">
                <p><strong>Zvolení zákazníci: </strong><span id="selected-count">0</span> <i>(Vybraní, ktorí majú emailovú adresu)</i></p>
                <div id="selected-names" style="max-height:100px;overflow-y:auto;padding:10px;background:#f5f5f5;border:1px solid #ddd;"></div>
            </div>
        </div>

        <div class="custom-dialog--footer">
            <div class="buttons-container">
                <button type="button" id="close-modal-btn" class="button button-secondary button-large">Zrušiť</button>
                <button type="button" id="send-bulk-message" class="button button-primary button-large">Odoslať</button>
            </div>
        </div>

    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Handle bulk action selection
        $('#doaction, #doaction2').click(function(e) {
            var selected_action = $(this).prev('select').val();
            if (selected_action === 'send-bulk-email') {
                e.preventDefault();

                // Get selected customers info
                var selected_posts = [];
                var selected_names = [];
                $('input[name="post[]"]:checked').each(function() {
                    var customerName = $(this).closest('tr').find('.column-title .row-title').text();
                    var customerEmail = $(this).closest('tr').find('.column-email').text();
                    if(customerEmail.indexOf('@') !== -1) {
                        selected_posts.push($(this).val());
                        selected_names.push(`${customerName} (${customerEmail})`);
                    }
                });

                // Update modal with selected customers info
                $('#selected-count').text(selected_posts.length);
                $('#selected-names').html(
                    selected_names.map(function(name) {
                        return '<div style="padding:2px 0;">' + name + '</div>';
                    }).join('')
                );

                $('#send-bulk-message-modal').addClass("shown");
            }
        });

        // Close modal
        $('#close-modal, #close-modal-btn').click(function() {
            $('#send-bulk-message-modal').removeClass("shown");
        });

        // Send messages
        $('#send-bulk-message').click(function() {
            var selected_posts = [];
            $('input[name="post[]"]:checked').each(function() {
                selected_posts.push($(this).val());
            });

            if(selected_posts.length === 0) {
                alert('Vyberte aspoň jedného zákazníka.');
                return;
            }

            if($.trim($('#message-subject').val()) === '') {
                alert('Predmet emailu nie je vyplnený.');
                return;
            }

            var messageContent;
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('message-content')) {
                messageContent = tinyMCE.get('message-content').getContent();
            } else {
                messageContent = $('#message-content').val();
            }

            if($.trim(messageContent) === '') {
                alert('Obsah správy nie je vyplnený.');
                return;
            }

            var data = {
                action: 'send_bulk_message',
                security: '<?php echo wp_create_nonce("send-bulk-message-nonce"); ?>',
                post_ids: selected_posts,
                subject: $('#message-subject').val(),
                message: messageContent
            };

            // Disable send button and show loading state
            var $sendButton = $(this);
            $sendButton.prop('disabled', true).text('Odosielam...');

            $.post(ajaxurl, data)
                .done(function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        $('#send-bulk-message-modal').removeClass("shown");
                    } else {
                        alert('Error: ' + (response.data.message || 'Unknown error occurred'));
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = 'Error occurred: ';
                    if (jqXHR.responseJSON?.data) {
                        errorMessage += jqXHR.responseJSON.data.message;
                    } else {
                        errorMessage += textStatus || 'Unknown error';
                    }
                    alert(errorMessage);
                })
                .always(function() {
                    $sendButton.prop('disabled', false).text('Odoslať');
                });
        });
    });
</script>