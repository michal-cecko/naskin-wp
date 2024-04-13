<?php /* Template Name: Domovská stránka */ ?>
<?php get_header(); ?>

<div class="notification-container">
    <?php if (isset($_GET['c']) && $_GET['c'] == "1") showNotification("Vaša rezervácia bola úspešne zrušená.", "success") ?>
</div>

<?php get_footer(); ?>
