<?php
get_header();
?>
<div class="container-fluid">
    <div class="category">
        <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
            <b>
                Class 10
            </b>
            <div class="dots mb-4 d-flex justify-content-center">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </h1>
    </div>
    <?php
    wp_nav_menu(array(
        'theme_location'  => 'category_class10',
        'container_class' => 'subject-page',
        'menu_class' => 'list-group ',
        'li_class' => 'list-group-item text-capitalize',
        'a_class' => 'list-group-item list-group-item-action',

    ));
    ?>
</div>
<?php get_footer() ?>