<!-- Banner -->
<div id="banner">
	<div class="wrapper">
        <div class="non_ban">
            <figure>
                <?php if($page == "Our Shop") { ?>
                    <img src="images/slider/nonhome//nonhome-1027446970.jpg" alt="a woman smiling while carrying shopping bags" />
                <?php } elseif ($page == "Products") { ?>
                    <img src="images/slider/nonhome/nonhome-1427176361.jpg" alt="a happy woman carrying shopping bags" />
                <?php } elseif ($page == "On Sale") { ?>
                    <img src="images/slider/nonhome/nonhome-765800779.jpg" alt="a woman carrying shopping bags" />
                <?php } elseif ($page == "New Arrivals") { ?>
                    <img src="images/slider/nonhome/nonhome-709224775.jpg" alt="a woman smiling" />
                <?php } elseif ($page == "Contact Us") { ?>
                    <img src="images/slider/nonhome/nonhome-705648562.jpg" alt="woman carrying a shoppingbag" />
                    <?php } else {?>
                    <img src="images/slider/nonhome/nonhome-417602179.jpg" alt="a woman with bags" />
                <?php } ?>
            </figure>
            <div class="page_title"><?php echo $page; ?></div>
        </div>
    </div>
</div>