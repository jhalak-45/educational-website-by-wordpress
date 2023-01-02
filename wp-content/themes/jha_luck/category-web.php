<?php get_header() ?>
<div class="container-fluid p-0">
    <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
        <b>
            Web Development
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
    <?php
    $args = array(
        'post_category' => 'web',
        'post_per_page' => '10',

    )
    ?>



        <div class="blog-container">
            <?php if (have_posts($args)) : ?>
                <?php while (have_posts($args)) : the_post($args); ?>
                    <div class="blog shadow-sm">
                        <div class="blog-thumbnail">
                            <a href="<?php the_permalink() ?>">
                                <img src="<?php echo  the_post_thumbnail_url() ?>" class="img-fluid">
                            </a>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink() ?>">

                                <h2 class="blog-title"><?php the_title() ?></h2>
                            </a>
                            <span>
                                <div class="fb-share-button" data-href="https://jhalakdhami.com.np/" data-layout="button_count" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>
                            </span>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
</div>

<?php get_footer() ?>