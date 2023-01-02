<?php get_header(); ?>
<div class="container-fluid">
    <div class="contact-page">
        <h1 class="page-title  mb-0 text-capitalize text-center p-3 pt-5 ">
            <b>
                Contact Us
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
    <div class="contact row">
        <div class="col-md-6 p-2 " data-aos="fade-left">
            <h3 class="h3 caption " data-aos="fade-right">Get Into Touch</h3>
            <div class="address-detail ">
                <div class="cont">
                    <div class="detail col-md-6 shadow " data-aos="fade-left">
                        <div class="icon text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M12 2C7.589 2 4 5.589 4 9.995 3.971 16.44 11.696 21.784 12 22c0 0 8.029-5.56 8-12 0-4.411-3.589-8-8-8zm0 12c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"></path>
                            </svg></div>
                        <h4 class="address text-center cap ">Address</h4>
                        <p class="text"><?php the_field('address') ?></p>

                    </div>
                    <div class="detail col-md-6 shadow" data-aos="fade-up-right">
                        <div class="icon text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M20 10.999h2C22 5.869 18.127 2 12.99 2v2C17.052 4 20 6.943 20 10.999z"></path>
                                <path d="M13 8c2.103 0 3 .897 3 3h2c0-3.225-1.775-5-5-5v2zm3.422 5.443a1.001 1.001 0 0 0-1.391.043l-2.393 2.461c-.576-.11-1.734-.471-2.926-1.66-1.192-1.193-1.553-2.354-1.66-2.926l2.459-2.394a1 1 0 0 0 .043-1.391L6.859 3.513a1 1 0 0 0-1.391-.087l-2.17 1.861a1 1 0 0 0-.29.649c-.015.25-.301 6.172 4.291 10.766C11.305 20.707 16.323 21 17.705 21c.202 0 .326-.006.359-.008a.992.992 0 0 0 .648-.291l1.86-2.171a1 1 0 0 0-.086-1.391l-4.064-3.696z"></path>
                            </svg></div>
                        <h4 class="phone text-center cap">Phone</h4>
                        <p class="text"><?php the_field('phone_number') ?></p>

                    </div>
                </div>
                <div class="cont">

                    <div class="detail col-md-6 shadow " data-aos="fade-left">
                        <div class="icon text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4.7-8 5.334L4 8.7V6.297l8 5.333 8-5.333V8.7z"></path>
                            </svg></div>
                        <h4 class="email text-center cap">Email</h4>
                        <p class="text"><?php the_field('email') ?></p>

                    </div>
                    <div class="detail col-md-6 shadow " data-aos="fade-right">
                        <div class="icon text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm3.293 14.707L11 12.414V6h2v5.586l3.707 3.707-1.414 1.414z"></path>
                            </svg></div>

                        <h4 class="opening-hour text-center cap">Opening Hours</h4>
                        <p class="text"><?php the_field('opening_hours') ?></p>

                    </div>
                </div>
            </div>
            <div class="map-image p-2 shadow" data-aos="fade-up-right">
                <h4 class="h4 caption"> Our Clinic Map </h4>
                <img src="<?php the_field('map_image') ?>">
            </div>
        </div>
        <div class="col-md-6 contact-form  text-capitalize shadow" data-aos="fade-down-right">
            <h4 class="caption h4">Leave Your Suggestion</h4>
            <div class="form d-flex justify-content-center">
                <?php echo do_shortcode('[contact-form-7 id="111" title="Contact form"]'); ?>
            </div>

        </div>
    </div>
    <div class="map p-2 my-2 shadow" data-aos="fade-down-left">
        <h4 class="h4 caption">Our Location</h4>
        <iframe src="<?php the_field('location_url') ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>



</div>
<?php get_footer(); ?>