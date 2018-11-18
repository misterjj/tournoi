$(function () {
  jQuery(window).on('scroll', _.throttle(function(event) {
    if (($(this).scrollTop()) >= 100) {
      $('nav.navbar').addClass('scrolled');
    } else {
      $('nav.navbar').removeClass('scrolled');
    }
  }, 100));
});