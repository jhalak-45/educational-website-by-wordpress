<?php get_header() ?>
<div class="container-fluid p-0">
    <div class="row w-100 h-auto">

        <div class="single-page col-md-9">
            <h2 class="h2"><?php the_title() ?></h2>
            <img src="<?php the_post_thumbnail_url(); ?>" height="auto" width="100%">
            <?php the_excerpt() ?>
            <div class="content">
                <?php the_content() ?>
            </div>
        </div>
        <div class="col-md-3 p-0">
            <?php get_sidebar() ?>
        </div>
    </div>
</div>
<?php get_footer()?>