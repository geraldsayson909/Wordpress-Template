<!-- HEADER -->

<?php if( !isset($_SESSION['visited']) ){
$_SESSION['visited'] = true;?>
<?php if(is_front_page()){ ?>
<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-close">×</span>
    </div>

  <div class="modal-body">
    <div class="container">
      <h2>Corona Virus (COVID 19) Message</h2>
      <p>Dummy text here on the site for the readers only. Dummy text here on this site for the readers only. Dummy contents for the readers to read now.</p>
    </div>
  </div>

    <div class="modal-footer">
    </div>
  </div>

</div>
<?php } ?>
<?php } ?>

<!-- Plugins -->

// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
//var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("modal-close")[0];

// When the user clicks the button, open the modal
//btn.onclick = function() {
//	modal.style.display = "block";
//}

$(document).ready(function(){

  // open the modal on load
  if(screen.width >= 320)
  {	modal.style.display = "block";
    $('body').addClass('popupfix');}
  else{
    modal.style.display = "none";
    $('body').removeClass('popupfix');
  }

});

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
  $('body').removeClass('popupfix');
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    $('body').removeClass('popupfix');
  }
}

<!-- CSS -->

.modal { display: none; position: fixed; z-index: 99999999; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4); }

/* Modal Content */
.modal-content { position: relative; background-color: #fefefe; margin: auto; padding: 0; border: 1px solid #888; width: 80%; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19); -webkit-animation-name: animatetop; -webkit-animation-duration: 0.4s; animation-name: animatetop; animation-duration: 0.4s }
.modal-content { background: linear-gradient(180deg, #51D5FF -20%, #00BAF6 126%); margin: 24px auto 0; padding: 20px; width: 100%; max-width: 779px; min-height: 421px; color: #fff; text-align: center; top: 50%; bottom: 0; right: 0; left: 0; transform: translate(0, -50%); border-radius: 40px;}

/* Add Animation */
@-webkit-keyframes animatetop {
from {top:-300px; opacity:0}
to {top:0; opacity:1}
}

@keyframes animatetop {
from {top:-300px; opacity:0}
to {top:0; opacity:1}
}

/* The Close Button */
.modal-close { color: white; float: none; font-size: 35px; font-weight: 400; background: linear-gradient(180deg, #51D5FF -20%, #00BAF6 126%);width: 49px;height: 49px;position: absolute;right: 30px;top: 30px;z-index: 1;border-radius: 50%;display: flex;justify-content: center;align-items: center;}

.modal-close:hover,
.modal-close:focus { color: #F1D30F; text-decoration: none; cursor: pointer; }
.modal-header { padding: 0 16px; background:none; color: white; position: relative;}
.modal-header h2{font-family: 'Southern Aire'; font-size: 99px; line-height: 100%; }

.modal-body {padding: 142px 10px 20px;width: 751px;background: #fff;min-height: 399px;border-radius: 40px;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);max-width: 97%;}
.modal-body  .container{height: 100%;color: #2c2c2c;}
.modal-body  .container h2{color: #2c2c2c;font-size: 26px;line-height: 100%;font-weight: 600;margin-bottom: 33px;}
.modal-body  .container p{color: #333;max-width: 100%;margin: 0 auto;width: 460px;line-height: 37px;}

.modal-footer { padding: 0 16px; background:none; color: white; }

.popupfix {position: fixed; top: 0; left: 0; right: 0;}
