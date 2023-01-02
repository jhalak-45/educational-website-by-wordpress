<?php get_header() ?>
<div class="container-fluid p-0">
    <h1 class="page-title mb-0 text-capitalize text-center p-3 pt-5 ">
        <b class="p-2">All Notices</b>
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
    <div class="notice-container">
        <?php
        //$count_posts = wp_count_posts('notices');
        //$total_posts = $count_posts->publish;
        //echo $total_posts;//counts the post in custom post type
        ?>
        <?php
        $args = array(
            'post_type' => 'notices',
            'posts_per_page' => 10
        );
        $the_query = new WP_Query($args);
        ?>
        <div class="notice">
            <table class="table  table-hover table-striped ">
                <thead class="pb-3" style="background-color:#0db296; height:60px; color:#ffffff;">
                    <tr>
                        <th scope="col">Notice Title </th>
                        <th scope="col">Published Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>


                    <?php if ($the_query->have_posts()) : ?>
                        <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                            <tr>

                                <td><?php the_title() ?></td>
                                <td><?php echo Get_the_date() ?></td>
                                <td>
                                    <a class="btn  m-1 view-btn" href="<?php the_permalink() ?>" style="    background-color: #072f60; color:#ffffff">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a class="btn  download-btn " href="<?php the_post_thumbnail_url() ?>" download style="background-color:#0db296;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M18.944 11.112C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.61 5.757 9.149 3.609 9.792 2 11.82 2 14c0 2.657 2.089 4.815 4.708 4.971V19H17.99v-.003L18 19c2.206 0 4-1.794 4-4a4.008 4.008 0 0 0-3.056-3.888zM8 12h3V9h2v3h3l-4 5-4-5z"></path>
                                        </svg>
                                    </a>
                                    
                                </td>

                            </tr>
                        <?php endwhile;
                        wp_reset_postdata(); ?>
                    <?php else :  ?>
                        <p><?php _e('Sorry, no notice are  created now.'); ?></p>
                    <?php endif; ?>


                </tbody>
            </table>
        </div>
    </div>
</div>
<?php get_footer() ?>








<!-- <div class="thumbnail">
                        <img src="<?php the_post_thumbnail_url() ?>" height="" width="100%">
                    </div>
                    <div class="notice-body">
                        <div class="notice-title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <span></span>
                        <div class="notice-excerpt">
                            <p class="excerpt"><?php the_excerpt(); ?></p>

                        </div> -->