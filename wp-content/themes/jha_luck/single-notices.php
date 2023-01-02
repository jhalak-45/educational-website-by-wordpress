<?php get_header() ?>
<div class="container-fluid">
    <div class="single-notice">
        <?php include 'social_icons.php';?>
        <div class="title mt-4 p-2">
            <h1>
                <?php the_title() ?>
            </h1>
        </div>

        <div class="notice_photo">
            <img src="<?php the_post_thumbnail_url() ?>" height="100%" width="100%">
        </div>
        <div class="download-btn">
            <a class="btn mt-2 btn-danger download-btn " href="<?php the_post_thumbnail_url() ?>" download>
                Download <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M18.944 11.112C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.61 5.757 9.149 3.609 9.792 2 11.82 2 14c0 2.657 2.089 4.815 4.708 4.971V19H17.99v-.003L18 19c2.206 0 4-1.794 4-4a4.008 4.008 0 0 0-3.056-3.888zM8 12h3V9h2v3h3l-4 5-4-5z"></path>
                </svg>
            </a>
        </div>
        <div class="excerpt">
            <p>
                <?php the_excerpt() ?>
            </p>
        </div>
        <div class="content">
            <p>
                <?php the_content() ?>


            </p>
        </div>
    </div>
</div>
<?php get_footer() ?>