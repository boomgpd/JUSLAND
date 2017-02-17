function page_list(){
	$("#waterfall").css('position','relative');
	var num = $("#waterfall li").length;//所有li的数量
	var small_index = 0;
	for($i=0;$i<num;$i++){
//		1.获取高度最低元素的序号;
//		2.获取对应的高度,和序号对4取余
		var left = 0;
		var top = 0;
		var arr = {};
		if($i<5){
			left = ($i%5)*240 +'px';
			top = 0+'px';
			$("#waterfall li").eq($i).css({'position':'absolute','left':left,'top':top,});
			var this_height = $("#waterfall li").eq($i).height();
			if(height > this_height){
				height = this_height;
			}
		}else{
			
			
		}
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
