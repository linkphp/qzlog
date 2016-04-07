$(document).ready(function(){var $Chance_LayerCont=$(".Chance_LayerCont");var $Chance_Layer_Close=$(".Chance_Layer_Close");$Chance_Layer_Close.click(function(){$Chance_LayerCont.slideUp()});Chance_Layer_Pop=function(){$Chance_LayerCont.slideDown()};if(window.addEventListener){window.addEventListener("load",Chance_Layer_Pop,false)}else{window.attachEvent("onload",Chance_Layer_Pop)}});

$(window).scroll(function () {
    if ($(window).scrollTop() >= 100) {
        $('.gotop').fadeIn(300);
    } else {
        $('.gotop').fadeOut(300);
    }
});

$('.gotop').click(function () {
    $('html,body').animate({ scrollTop: '0px' }, 800);
});