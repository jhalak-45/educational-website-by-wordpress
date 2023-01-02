<div class="about p-2">
    <h1 class="section-title  mb-0 text-capitalize text-center p-3 pt-5 ">
       <f>About Us</f>
    </h1>
    <div class="row p-2 h-auto">
        <div class="col-md-6 pic bg-light p-3">
            <img src="<?php the_field('about_page_image', 10) ?>">

        </div>
        <div class="col-md-6 bg-light text">
            <p>
                <?php the_field('about_text', 10) ?>
            </p>
            <a href="./about" class=" btn text-light " onclick="show()" style="background-color:#0b8b6e;"> Read More..</a>
        </div>
    </div>
</div>