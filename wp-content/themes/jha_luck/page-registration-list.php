<?php get_header() ?>

<div class="container-fluid p-4 pt-4 bg-light">
  <div class="top-buttn">
    <a href="<?php  echo site_url() ?>/registration" class="btn mb-5 btn-primary">Add New</a>
  </div>

  <table class="table table-striped text-capitalize shadow w-100 bg-light px-auto table-hover">
    <thead class="bg-primary text-light"style="height:60px;">
      <tr>
        <th scope="col" class="text-uppercase">id</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email Address</th>
        <th scope="col">Phone number</th>
        <th scope="col">DOB</th>
        <th scope="col">Gender</th>
        <th scope="col">message</th>

      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM registration");
        foreach ($result as $reg) {
          echo '<tr>';
          // echo'<pre>';
          // var_dump($reg);
          // echo'</pre>';

        ?>
          <td><?php echo $reg->id; ?></td>
          <td><?php echo $reg->fname; ?></td>
          <td><?php echo $reg->lname; ?></td>
          <td class="text-lowercase"><?php echo $reg->address; ?></td>
          <td><?php echo $reg->number; ?></td>
          <td><?php echo $reg->dob; ?></td>
          <td><?php echo $reg->gender; ?></td>
          <!-- <td>
            <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($reg->image);?>" height="100px" width="110px">
          </td> -->
          <td class="text-lowercase"><?php echo $reg->msg; ?></td>
         
        <?php
        }
        echo '</tr>';
        ?>
    </tbody>
  </table>
</div>
<?php get_footer() ?>