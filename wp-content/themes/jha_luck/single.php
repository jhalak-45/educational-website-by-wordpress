
<?php

$post =$wp_query->post;

if (in_category('blog',$post->ID)){
    include(TEMPLATEPATH . '/blog-single.php');
} 
 else {
    include(TEMPLATEPATH . '/single-web.php');
}
?>
