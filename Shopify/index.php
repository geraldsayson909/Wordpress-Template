<?php $page = 'Home'; 
$is_home = true;
?>

<?php
set_include_path ('includes');
include "head.php";
include "header.php";
include "nav.php";
include "banner.php";
include "middle.php";
?>

<!-- Main -->
<div id="main_area">
    	<div class="wrapper">
	<div class="main_holder">
    <div class="main_con animatedParent animateOnce">
    		<main>
          <h1 class="h1_title"><span>Introducing </span>The Woosah Box</h1>
          <p><mark class="comp">Woosah:</mark> to tell someone to calm down or relax. </p>

          <p><mark class="comp">The Woosah Box</mark> offers a subscription box for mugs, tumblers, and shirts. We provide great design, comfort, and functionality in one box. At <mark class="comp">Woosah</mark>, our products help you achieve the relaxation you want. After all, what’s better than to enjoy a delicious drink in your favorite mug or tumbler? And while you’re at it, you also get to lounge at home in a comfy shirt. Pop on a movie or two and call it a day. </p>
          

    		</main>
        

        <div class="main_boxes">  
          <a href="mugs-and-shirts-shop-free-delivery.php" class="main_box1 animated zoomIn slow">
            <h2>Free Delivery</h2>
          </a>

          <a href="mugs-and-shirts-shop-discount-cards.php" class="main_box2 animated zoomIn slow delay-500">
            <h2>Discount Cards</h2>
          </a>
        </div>
    </div> <!-- .main_con -->

    <figure class="main_bg">
      <img src="images/main-bg.jpg" alt="a woman smiling"/>
    </figure>

	</div>
	<div class="clearfix"></div>
    	</div>
    </div>
  <!-- End Main -->




<?php
include "bottom.php";
include "footer.php";
?>