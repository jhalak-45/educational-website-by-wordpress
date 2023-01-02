<?php get_header() ?>
<div class="container-fluid p-0 ">


    <div class="front-page mt-0 my-0 px-2">
        <div class="row">
            <div class="col-md-6 text-box">
                <div class="dots mb-4">
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
                <h5 class="sub-caption mt-5">
                    DEVELOPED BY TOP TEACHERS
                </h5>
                <h2 class="caption mt-0">
                    Experience a learning platform that take you next level
                </h2>
                <div class="btn p-0 mt-5">
                    <a href="" class="explore-btn">
                        Explore all Courses
                    </a>
                </div>

            </div>
            <div class="col-md-6 image-box d-flex justify-content-center">
                <img src="<?php echo get_template_directory_uri() ?>/front-page-bg.png" height="100%" width="100%">
            </div>
        </div>

    </div>
    <section class="course-section">
        <h1 class=" mt-0 text-center bg-light p-2 pb-2 ">
            <b class=" rounded-circle text-light p-3" style="background-color:#00b489"><i class='bx bx-book-open bx-tada' style='color:#ffffff'></i></b>
            <b class=" rounded-circle text-light p-3" style="background-color:#00b489"><i class='bx bx-book-open bx-tada' style='color:#ffffff'></i></b>
            <b class=" rounded-circle text-light p-3" style="background-color:#00b489"><i class='bx bx-book-open bx-tada' style='color:#ffffff'></i></b><b class=" rounded-circle text-light p-3" style="background-color:#00b489"><i class='bx bx-book-open bx-tada' style='color:#ffffff'></i></b>
            <b class=" rounded-circle text-light p-3" style="background-color:#00b489"><i class='bx bx-book-open bx-tada' style='color:#ffffff'></i></b>
        </h1>
        <div class="title">
            <h2 class="section-title p-2 text-center">
                <f> Our Courses</f>
            </h2>
        </div>
        <div class="courses">

            <?php
            $wp_query = new WP_Query(array(
                'post_type' => 'courses',
                'post_per_page' => 10,

            )); ?>
            <?php if ($wp_query->have_posts()) : ?>
                <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                    <div class="card shadow" style="width:18rem;">
                        <img src="<?php the_field('course_image') ?>" height="250px" width="100%">
                        <div class="card-body">
                            <h5 class="card-title"><?php the_field('course_name') ?></h5>
                        </div>
                    </div>
            <?php endwhile;
             wp_reset_postdata(); ?> 
             <?php endif; ?>




        </div>

    </section>
    <section class="course-section mt-0">
        <div class="title">
            <h2 class="section-title p-2 mt-0 text-center">
                <f> Our Classes</f>
            </h2>
        </div>
        <div class="courses">
        <?php
            $wp_query = new WP_Query(array(
                'post_type' => 'class',
                'post_per_page' => 10,

            )); ?>
            <?php if ($wp_query->have_posts()) : ?>
                <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                    <div class="card shadow " style="width:18rem;">
                        <img src="<?php the_field('class_image') ?>" height="250px" width="100%">
                        <div class="card-body">
                            <h5 class="card-title"><?php the_field('class_name') ?></h5>
                        </div>
                    </div>
            <?php endwhile;
             wp_reset_postdata(); ?> 
             <?php endif; ?>
            


        </div>

    </section>
    <section class="course-section">
        <?php get_template_part('partials/section','about')?>
    </section>
    <section class="course-section mt-0">
        <div class="title">
            <h2 class="section-title p-2 mt-0 text-center">
                <f> Our
                    blogs
                </f>
            </h2>
        </div>

        <?php
        $wp_query = new WP_Query(array(
            'post_type' => 'post',
            'category_name' => 'blog',
            'posts_per_page' => 5,
            'paged' => $paged,
        ));
        ?>
        <div class="blog-container p-2">

            <?php if ($wp_query->have_posts()) : ?>
                <!-- begin loop -->

                <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

                    <div class="blog shadow-sm m-1" style="width:18rem;">
                        <div class="blog-thumbnail">
                            <a href="<?php the_permalink() ?>">
                                <img src="<?php echo  the_post_thumbnail_url() ?>" class="img-fluid">
                            </a>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink() ?>">

                                <h2 class="blog-title"><?php the_title() ?></h2>
                            </a>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>

    </section>


</div>
<?php get_footer() ?>