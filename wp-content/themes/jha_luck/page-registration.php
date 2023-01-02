<?php


//Template Name:REgistration
// 


get_header()
?>

<?php
if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phn = $_POST['phn'];
    //    $province =$_POST['province'];
    //    $dist =$_POST['dist'];
    $dob = $_POST['dob'];
    //    $ctn =$_POST['cn'];
    //    $gen =$_POST['gender'];
    $msg = $_POST['msg'];
    // $img =$_POST['img'];
    // $img = wp_upload_bits($_FILES["img"]["name"], null, file_get_contents($_FILES["img"]["tmp_name"]));




    global $wpdb;


    $sql = $wpdb->prepare(
        " INSERT INTO `registration` (`id`, `fname`, `lname`, `address`, `number`,  `dob`, `msg`)VALUES (NULL, '$fname', '$lname', '$email','$phn', '$dob', '$msg ')"
    );

    $wpdb->query($sql);

    if ($sql) {
        echo '<div class="alert align-center px-auto col-md-8 alert-primary alert-dismissible fade show" role="alert">Registration Success</div>';
        // wp_redirect(site_url());
        // exit;
    }
}
?>
<div class="container col-md-8 p-3">
    <form method="POST" enctype="multipart/form-data" class="form-control form shadow p-3">
        <h3 class="h3  text-center text-uppercase mt-2 py-4 form-title">Register your details</h3>

        <div class="row py-1">
            <div class=" col-md-6 form-group mr-2 justify-content-center">
                <label for="fname">First Name:</label>
                <input type="text" class="form-control shadow-none rounded-1" id="fname" name="fname" placeholder="Enter your first name" required>
            </div>
            <div class="col-md-6  form-group justify-content-center">
                <label for="lname">Last Name:</label>
                <input type="text" class="form-control shadow-none rounded-1" id="fname" name="lname" placeholder="Enter your last name" required>
            </div>
        </div>


        <div class="row py-1">
            <div class=" col-md-6 form-group mr-2 justify-content-center">
                <label for="email">Email Address:</label>
                <input type="email" class="form-control shadow-none rounded-1" name="email" id="email" placeholder="Enter your valid email address" required>
            </div>
            <div class="col-md-6 form-group justify-content-center">
                <label for="con">Contact Number:</label>
                <input type="number" class="form-control shadow-none rounded-1" name="phn"  id="con" placeholder="Enter your contact number" required>
            </div>
        </div>
        <!-- <div class="row py-1">
                        <label for="district">Full Address:</label><br>

                  </div> -->

        <div class="row py-1">
            <div class=" col-md-6 form-group mr-2 justify-content-center">
                <label for="dob">Birth Date:</label>
                <input type="date" class="form-control shadow-none rounded-1" name="dob" id="dob" required>
            </div>
            <div class="col-md-6 form-group justify-content-center">
                <label for="cn">Citizenship Number:</label>
                <input type="number" class="form-control shadow-none rounded-1" name="cn" id="cn" placeholder="Enter your Citizenship number" min="10000" max="1000000" required>
            </div>
        </div>
        <div class="row py-1">
            <div class=" col-md-12 form-group mr-2 justify-content-center">
                <label for="province">Full Address</label>
                <input type="text" class="form-control shadow-none rounded-1" name="address" id="address">
            </div>
        </div>
        <div class="row py-1">
            <label for="">Gender:</label>
            <div class="col-md-5 row">
                <div class="col custom-control custom-radio custom-control-inline ml-5"> <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input">
                    <label class="custom-control-label shadow-none" name="gender" value="M" for="customRadioInline1">Male</label>
                </div>
                <div class="col  custom-control custom-radio custom-control-inline shadow-none">
                    <input type="radio" id="customRadioInline2" name="customRadioInline1" class=" shadow-none custom-control-input">
                    <label class="custom-control-label" for="customRadioInline2" value="F" name="gender">Female</label>

                </div>
                <div class="col custom-control custom-radio custom-control-inline shadow-none">
                    <input type="radio" id="customRadioInline2" name="customRadioInline1" class=" shadow-none custom-control-input">
                    <label class="custom-control-label" for="customRadioInline2" value="O" name="gender">Other</label>

                </div>
            </div>

        </div>

        <div class="row py-1 ml-2">

            <div class="row form-group py-1  ">
                <label for="">Select your Image:</label>
                <div class="col form-group justify-content-end px-auto">
                    <input type="file" name="img" class="form-control rounded-1 shadow-none" id="img">
                </div>
            </div>

        </div>
        <div class="row py-1">
            <div class=" col form-group  justify-content-center">
                <label for="text">Leave your message:</label>
                <textarea class="form-control shadow-none rounded-1 " name="msg" id="text" style="resize: none; height:170px;"></textarea>
            </div>

        </div>


        <div class="row py-2">
            <div class=" col-md-6 form-group mr-2  d-flex justify-content-center">
                <!-- <input type="reset" class="form-control btn btn-danger  rounded-1 shadow-none" id="clear" Value="Clear"> -->
            </div>
            <div class="col-md-6 form-group  mr-2 sm-mt-2  d-flex justify-content-center">
                <input type="submit" value="Register" name="submit" class="form-control rounded-1 submit shadow-none btn btn-primary  font-weight-bold" id="reg">
            </div>
        </div>
    </form>
</div>
<?php get_footer() ?>