<div class="container-fluid p-0 bg-light">
    <h2 class="sidebar-title p-2">latest Blogs</h2>
    <?php
    $wp_query = new WP_Query(array(
        'post_type' => 'post',
        'category_name' => 'blog',
        'posts_per_page' => 5,
        'paged' => $paged,
    ));
    ?>
    <div class="blog-container p-0">

        <?php if ($wp_query->have_posts()) : ?>
            <!-- begin loop -->

            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

                <div class="blog shadow-sm m-1">
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




    <?php wp_reset_postdata(); ?>
    <h2 class="sidebar-title p-2">
        Web Development
    </h2>
    <?php
    $wp_query = new WP_Query(array(
        'post_type' => 'post',
        'category_name' => 'web',
        'posts_per_page' => 5,
        'paged' => $paged,
    ));
    ?>
    <div class="blog-container p-0">

        <?php if ($wp_query->have_posts()) : ?>
            <!-- begin loop -->

            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

                <div class="blog shadow-sm m-1">
                    <!-- <div class="blog-thumbnail">
                        <a href="<?php the_permalink() ?>">
                            <img src="<?php echo  the_post_thumbnail_url() ?>" class="img-fluid">
                        </a>
                    </div> -->
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
    <?php wp_reset_postdata(); ?>




    <div class="classes">
        <?php dynamic_sidebar('footer-1')?>

    </div>

</div>