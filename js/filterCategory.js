

/*------------------- GALLERY GRID ------------------*/
$('#category-menu').on('click', 'a', function(e){

  var listSelect = $('.cat-item');
  $('.cat-item a').removeClass('fadeInUp')
  $('.cat-item a').removeClass('active');
  $(this).addClass('active');
  listSelect.removeClass('selected');
  $(this).parent().addClass('selected');
  e.preventDefault();
  var a = $(this).attr('href');
  a = a.substr(1);
  $('.ajax-call').each(function() {
    if($(this).hasClass(a) || a == 'all'){
      TweenLite.fromTo ($(this) , 0.3, {scale:"0"}, {scale:"1",display:'block'}) 
    }else{
      TweenLite.fromTo ($(this) , 0.3, {scale:"0.5"}, {scale:"0",display:'none'})  	
    }
  });
});
  

//transition on menu

var group = document.querySelector("#category-menu");
var nodes = document.querySelectorAll(".cat-item");
var total = nodes.length;
var ease  = Power1.easeInOut;
var boxes = [];

for (var i = 0; i < total; i++) {
  
  var node = nodes[i];
  
  // Initialize transforms on node
  TweenLite.set(node, { x: 0 });
   
  boxes[i] = {
    transform: node._gsTransform,
    x: node.offsetLeft,
    y: node.offsetTop,
    node    
  };
} 

group.addEventListener("click", layout);
group.addEventListener("click", layout);

function layout() {
    
  for (var i = 0; i < total; i++) {
    
    var box = boxes[i];
        
    var lastX = box.x;
    var lastY = box.y;
    
    box.x = box.node.offsetLeft;
    box.y = box.node.offsetTop;
    
    // Continue if box hasn't moved
    if (lastX === box.x && lastY === box.y) continue;
    
    // Reversed delta values taking into account current transforms
    var x = box.transform.x + lastX - box.x;
    var y = box.transform.y + lastY - box.y;  
    
    // Tween to 0 to remove the transforms
    TweenLite.fromTo(box.node, 0.5, { x, y }, { x: 0, y: 0, ease });    
  } 
}


