// ======== Scroll Counter ========
var iterator = 0;
$(window).scroll(function() {

  var oTop = $('#counter').offset().top - window.innerHeight;
  if (iterator == 0 && $(window).scrollTop() > oTop) {
    $('.counter-value').each(function() {
      var $this = $(this),
        countTo = $this.attr('data-count');
      $({
        countNum: $this.text()
      }).animate({
          countNum: countTo
        },

        {
          duration: 3000,
          easing: 'swing',
          step: function() {
            $this.text(Math.floor(this.countNum));
          },
          complete: function() {
            $this.text(this.countNum);
          }

        });
    });
    iterator = 1;
  }
});

// ======== Direction aware hover effect =======

//Detect Closest Edge
function closestEdge(distLeft,distTop,w,h){
  var distBottom = (h - distTop);
  var distRight = (w - distLeft);
  var min = Math.min(distTop, distBottom, distLeft, distRight);
  switch (min) {
      case distLeft:
          return "left";
      case distRight:
          return "right";
      case distTop:
          return "top";
      case distBottom:
          return "bottom";
  }
}

var boxes = $('.card-team');

if($(window).width()>576){

  for(var i = 0; i < boxes.length; i++){
    boxes[i].onmouseenter = function(e){
        var edge = closestEdge(e.offsetX, e.offsetY, $(this).width(), $(this).height());
        var overlay = this.childNodes[3];
        var image = this.childNodes[1];
  
        switch(edge){
            case "left":
                //tween overlay from the left
                overlay.style.top = "0%";
                overlay.style.left = "-100%";
                TweenMax.to(overlay, .4, {left: '0%'});
                TweenMax.to(image, .5, {scale: 1.2});
                break;
            case "right":
                overlay.style.top = "0%";
                overlay.style.left = "100%";
                //tween overlay from the right
                TweenMax.to(overlay, .4, {left: '0%'});
                TweenMax.to(image, .5, {scale: 1.2});
                break;
            case "top":
                overlay.style.top = "-100%";
                overlay.style.left = "0%";
                //tween overlay from the right
                TweenMax.to(overlay, .4, {top: '0%'});
                TweenMax.to(image, .5, {scale: 1.2});
                break;
            case "bottom":
                overlay.style.top = "100%";
                overlay.style.left = "0%";
                //tween overlay from the right
                TweenMax.to(overlay, .4, {top: '0%'});
                TweenMax.to(image, .5, {scale: 1.2});
                break;
        }
    };
  
   
    boxes[i].onmouseleave = function(e){
        var edge = closestEdge(e.offsetX, e.offsetY, $(this).width(), $(this).height());
        var overlay = this.childNodes[3];
        var image = this.childNodes[1];
  
        switch(edge){
            case "left":
                TweenMax.to(overlay, .4, {left: '-100%'});
                TweenMax.to(image, .5, {scale: 1.0});
                break;
            case "right":
                TweenMax.to(overlay, .4, {left: '100%'});
                TweenMax.to(image, .5, {scale: 1.0});
                break;
            case "top":
                TweenMax.to(overlay, .4, {top: '-100%'});
                TweenMax.to(image, .5, {scale: 1.0});
                break;
            case "bottom":
                TweenMax.to(overlay, .4, {top: '100%'});
                TweenMax.to(image, .5, {scale: 1.0});
                break;
        }
    };
  }
}