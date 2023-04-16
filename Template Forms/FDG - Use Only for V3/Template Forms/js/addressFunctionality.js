var prov = document.getElementById("prov");
var muni_city = document.getElementById("muni_city");
var state_con = document.getElementById("state_con");
var city_con = document.getElementById("city_con");
var zip_con = document.getElementById("zip_con");
var zip_ph_con = document.getElementById("zip_ph_con");

var	province = document.getElementById("province");
var	city = document.getElementById("city");
var state_usa = document.getElementById("state_usa");
var city_usa = document.getElementById("city_usa");
var zip_usa = document.getElementById("zip_usa");
var zip_ph = document.getElementById("zip_ph");

state_con.style.display = "none";
city_con.style.display = "none";
zip_con.style.display = "none";

state_usa.disabled = "disabled";
city_usa.disabled = "disabled";
zip_usa.disabled = "disabled";

province.disabled = false;
city.disabled = false;

$('select[name="Country"]').change(function(){
  if($(this).val() == "Philippines"){

    province.disabled = false;
    city.disabled = false;
    zip_ph.disabled = false;
    state_usa.disabled = true;
    city_usa.disabled = true;
    zip_usa.disabled = true;

    prov.style.display = "block";
    muni_city.style.display = "block";
    zip_ph_con.style.display = "block";
    state_con.style.display = "none";
    city_con.style.display = "none";
    zip_con.style.display = "none";

  } else if($(this).val() == "United States of America"){


    $("#country").removeClass("form_box_col3");
    $("#country").addClass("form_box_col2");

    province.disabled = true;
    city.disabled = true;
    zip_ph.disabled = true;
    state_usa.disabled = false;
    city_usa.disabled = false;
    zip_usa.disabled = false;

    prov.style.display = "none";
    muni_city.style.display = "none";
    zip_ph_con.style.display = "none";
    state_con.style.display = "block";
    city_con.style.display = "block";
    zip_con.style.display = "block";

  } else {

    zip_ph_con.style.display = "none";
    zip_con.style.display = "none";
    prov.style.display = "none";
    muni_city.style.display = "none";
    state_con.style.display = "none";
    city_con.style.display = "none";

    zip_ph.disabled = true;
    state_usa.disabled = true;
    city_usa.disabled = true;
    zip_usa.disabled = true;
    province.disabled = true;
    city.disabled = true;

  }
});
