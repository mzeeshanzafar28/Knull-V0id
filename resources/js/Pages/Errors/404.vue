<template #404>
    <!-- <AppLayout title="404"> -->
    <div class="wrapper">
      <div id="sky">
        <div id="moon"></div>
        <div id="stars"></div>
        <div id="clouds"></div>
      </div>
  
      <div id="middle-content">
        <div id="spaceship" class="swing">
          <div id="light"></div>
        </div>
        <div class="err-text swing-text">
          <h1>ERROR 404</h1>
          <p>Sorry, the page you're looking for could not be found.</p>
          <p onclick="window.history.go(-1)">Go Back</p>
        </div>
      </div>
  
      <div id="ground">
        <div id="mountain1"></div>
        <div id="mountain2"></div>
        <div id="mountain3"></div>
      </div>
    </div>
<!-- </AppLayout> -->
  </template>
  
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import jQuery from "jquery";
import $ from "jquery";
import"@/../css/style.css";
import"@/plax.js"; 
import"@/jquery.spritely-0.6.1.js"; 
import"@/jquery-animate-css-rotate-scale.js"; 

export default {
  name: "NotFoundPage",
  async mounted() {
    try {
        
        
function swingText() {

if (!$('.swing-text').hasClass('down')) {

    $('.swing-text').animate({
        rotate: '65deg',
        left: 190,
        bottom: 90
    }, 2000, function () {
        $('.swing-text').toggleClass('down');
    });
} else {

    $('.swing-text').animate({
        rotate: '70deg',
        left: 180,
        bottom: 100
    }, 2000, function () {
        $('.swing-text').toggleClass('down');
    });

}


setTimeout(swingText, 2000);
}

function swingSpaceship() {

if (!$('.swing').hasClass('down')) {

    $('.swing').animate({
        left: 5,
        top: 160
    }, 1500, function () {
        $('.swing').toggleClass('down');
    });
} else {

    $('.swing').animate({

        left: 0,
        top: 150
    }, 1500, function () {
        $('.swing').toggleClass('down');
    });

}


setTimeout(swingSpaceship, 1500);
}

$('#spaceship').animate({

left: 0,
top: 150
}, 2000, function () {
swingSpaceship();
setTimeout(function () {
    $('#light').fadeIn(2000, function () {

        $('.err-text').animate({
            rotate: '70deg',
            left: 180,
            bottom: 100
        }, 5000, function () {
            swingText();

        });
    });


}, 1000);
});




$('#clouds').pan({
fps: 25,
speed: 0.8,
dir: 'left',
depth: 300
});
$('#stars').pan({
fps: 25,
speed: 0.2,
dir: 'left',
depth: 400
});





$('#mountain1').plaxify({
"xRange": 3,
"yRange": 0
});
$.plax.enable();

$('#mountain2').plaxify({
"xRange": -3,
"yRange": 0
});

$('#mountain3').plaxify({
"xRange": 4,
"yRange": 0
});

$('#middle-content').height($(window).height());

    //   console.log("All scripts loaded successfully!");
    } catch (error) {
      console.error("Error loading scripts:", error);
    }
  },
};
</script>

  
  