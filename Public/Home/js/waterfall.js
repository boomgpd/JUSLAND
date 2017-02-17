window.onload = function() {
    waterfall('main', 'pin');
}
/*parent 父级id     pin具体瀑布流快 class 类名*/
function waterfall(parent, pin) {
    var oParent = document.getElementById(parent);	// 父级对象
    var aPin = getClassObj(oParent, pin);	// 获取存储块框pin的数组aPin
    var iPinW = aPin[0].offsetWidth;	// 一个块框pin的宽
	var num = Math.floor(1200 / iPinW);		//每行中能容纳的pin个数【窗口宽度除以一个块框宽度】
    oParent.style.cssText = 'width:' + num * iPinW + 'px;margin:0 auto;';	//设置父级居中样式：定宽+自动水平外边距

	$("#main").css('position','relative');
    var compareAarr = [];	//用于存储 每列中的所有块框相加的高度。
    for (var i = 0; i < aPin.length; i++) {	
		//遍历数组aPin的每个块框元素
        if (i < num) {
            compareAarr[i] = aPin[i].offsetHeight;	//第一行中的num个块框pin 先添加进数组compareHArr
        } else {
            var minH = Math.min.apply({}, compareAarr);	//数组compareHArr中的最小值minH
            var minKey = getMinKey(compareAarr, minH);
            compareAarr[minKey] += aPin[i].offsetHeight;
			setMoveStyle(aPin[i],minH,aPin[minKey].offsetLeft,i);

        }


    }
    // 拖拽
    // 拖拽
    // 拖拽
}

//设置运动风格 运动的对象   运动的top值  运动的left值
var startNum = 0;

function setMoveStyle(obj,top,left,index){
		if(index <= startNum){
			return;
		}
		obj.style.position = 'absolute';	
		obj.style.top = getTotalH()+'px';
		obj.style.left = 0 +'px';
		$(obj).stop().animate({
			top:top,
			left:left
		},1000);
		startNum = index; //更新索引
}




function checkScrollSite(){
	
	var srcollTop = document.documentElement.scrollTop || document.body.scrollTop;
	var documentH = document.documentElement.clientHeight;
	return  getLastH() <  srcollTop + documentH ? true : false;
	
}
function getLastH(){
	var oParent = document.getElementById('main');
    var aPin = getClassObj(oParent, 'pin');
	var lastPinH = aPin[aPin.length-1].offsetTop+Math.floor(aPin[aPin.length-1].offsetHeight/2);
	return lastPinH;
}
// 获得总高顿不过

function getTotalH(){
	var totalH = document.documentElement.scrollHeight || document.body.scrollHeight;	
	return totalH;
}
/*****
 *获取数组最小值的 键值
 *@param {Object} arr
 *@param {Object} minH
 */
function getMinKey(arr, minH) {
    for (key in arr) {
        if (arr[key] == minH) {
            return key;
        }
    }
}


/*****
 *通过class选择元素
 *@param {Object} parent 父级
 *@param {Object} className 类名
 */

function getClassObj(parent, className) {
    var obj = parent.getElementsByTagName('*');
    var result = [];
    for (var i = 0; i < obj.length; i++) {
        if (obj[i].className == className) {
            result.push(obj[i]);
        }
    }
    return result;
}
