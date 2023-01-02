<?php wp_footer() ?>
<footer class="footer pb-0">
    <div class="container-fluid px-auto pb-0">
        <div class="row ">
            <div class="col-md-4 px-auto">
                <h1 class="text-light"><?php bloginfo() ?></h1>
                <div class="about p-1">
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Porro doloremque culpa assumenda adipisci sit accusantium at,
                        delectus quia, obcaecati, modi excepturi itaque iure nisi nesciunt
                        voluptatum dolor vero provident saepe!

                    </p>
                </div>

            </div>
            <div class=" col-md-4 px-auto">
                <?php dynamic_sidebar('footer-center') ?>

            </div>
            <div class=" col-md-4 px-auto info ">
                <h4 class="title text-capitalize mb-3">Contact Us</h4>
                <div class="address">
                    <ul class="list">
                        <li class="item">
                            <i class="fas fa-map-marker mr-1"> </i> Dhangadhi-02 Kailali , Nepal
                        </li>
                        <li class="item">
                            <i class="fas fa-envelope mr-1"> </i> info@jhalakdhami.com.np
                        </li>
                        <li class="item">
                            <i class="fas fa-phone mr-1"> </i> +977-9849406142
                        </li>
                        <li class="item">
                            <i class="fas fa-earth mr-1"> </i> www.jhalakdhami.com.np
                        </li>

                    </ul>



                </div>
                <h5 class="title text-capitalize mb-3">follow us</h5>
                <section class="mb-4">
                    <!-- Facebook -->
                    <a class="btn text-white btn-floating m-1" style="background-color: #3b5998;" href="#!" role="button"><i class="fab fa-facebook-f"></i></a>

                    <!-- Twitter -->
                    <a class="btn text-white btn-floating m-1" style="background-color: #55acee;" href="#!" role="button"><i class="fab fa-twitter"></i></a>

                    <!-- Google -->

                    <!-- Instagram -->
                    <a class="btn text-white btn-floating m-1 rounded" style="background-color: #ac2bac;" href="#!" role="button"><i class="fab fa-instagram rounded"></i></a>

                    <!-- Linkedin -->
                    <a class="btn text-white btn-floating m-1" style="background-color: #0082ca;" href="#!" role="button"><i class="fab fa-linkedin-in"></i></a>
                    <!-- Github -->
                </section>

            </div>

        </div>
        <div class="d-flex justify-content-center">
            <hr class="w-75">
        </div>
        <div class="copy-right d-flex justify-content-center">
            <h5 class="h6 text-capitalize">&copy; 2022, copyright by <?php bloginfo() ?></h5>
        </div>
    </div>

</footer>
<!-- <script src="particle.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.3/particles.min.js"></script>
</body>

</html>