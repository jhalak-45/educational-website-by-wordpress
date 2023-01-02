<?php get_header() ?>
<canvas class="background"></canvas>

<div class="container-fluid p-0">
    <div class="about-page">
        <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
            <b>
                About Us
            </b>
            <div class="dots  d-flex justify-content-center">
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
    <div class="about p-0">
        <div class="row p-2 h-auto">
            <div class="col-md-6 pic">
                <img src="<?php the_field('about_page_image')?>">
            </div>
            <div class="col-md-6 text">
                <?php the_field('about_text')?>
            </div>
        </div>
    </div>


</div>
<?php get_footer() ?>