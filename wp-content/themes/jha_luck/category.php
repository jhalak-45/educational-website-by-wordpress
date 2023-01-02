<?php get_header() ?>
<div class="container-fluid">
    <div class="category-page">
        <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
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
        <div class="row lg-p-2 sm-p-0 p-0 h-auto">
            <div class="col-3">
                <div class="chapters">
                    <h1 class="chapters-btn">Chapters</h1>
                </div>
                <div class="chapter-titles">

                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <h2 class="title tablinks" onclick="openCity(event, '<?php the_ID() ?>')" id="<?php the_ID() ?>">
                                <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                            </h2>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-9 pt-5 mt-1">
                <div id="<?php the_ID() ?>" class="tabcontent">
                    <?php echo get_the_content() ?>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="clear-both">

</div>

<?php get_footer() ?>