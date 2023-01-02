<?php get_header() ?>
<div class="container-fluid p-0">
    <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
        <b>
            Our Blogs
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
    $wp_query = new Wp_Query(array(
        'post_type'=>'post',
        'category_name' => 'blog',
        'post_per_page' => '10',
        'paged'=>$paged,

    ));
    ?>


    <div class="blog-container">
        <?php if ($wp_query->have_posts()) : ?>
            <?php while ($wp_query->have_posts()) :  $wp_query->the_post(); ?>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M21 20V6c0-1.103-.897-2-2-2h-2V2h-2v2H9V2H7v2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2zM9 18H7v-2h2v2zm0-4H7v-2h2v2zm4 4h-2v-2h2v2zm0-4h-2v-2h2v2zm4 4h-2v-2h2v2zm0-4h-2v-2h2v2zm2-5H5V7h14v2z"></path>
                            </svg>
                            <?php echo get_the_date() ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="m7 17.013 4.413-.015 9.632-9.54c.378-.378.586-.88.586-1.414s-.208-1.036-.586-1.414l-1.586-1.586c-.756-.756-2.075-.752-2.825-.003L7 12.583v4.43zM18.045 4.458l1.589 1.583-1.597 1.582-1.586-1.585 1.594-1.58zM9 13.417l6.03-5.973 1.586 1.586-6.029 5.971L9 15.006v-1.589z"></path>
                                <path d="M5 21h14c1.103 0 2-.897 2-2v-8.668l-2 2V19H8.158c-.026 0-.053.01-.079.01-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2z"></path>
                            </svg>
                            <?php echo get_the_author() ?>

                        </span>
                        <div class="blog-excerpt">
                            <p class="excerpt"><?php the_excerpt() ?></p>
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <div class="pagination shadow-sm">
        <?php wp_pagenavi(); ?>
    </div>
</div>

<?php get_footer() ?>