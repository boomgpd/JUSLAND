window.onload = function(){
function alert_view(phpdata){
				switch(phpdata.name){
					case 'message':
						window.wxc.xcConfirm(phpdata.content, window.wxc.xcConfirm.typeEnum.info);
						break;
					case 'remind':
						window.wxc.xcConfirm(phpdata.content, window.wxc.xcConfirm.typeEnum.confirm);
						break;
					case 'warning':
						window.wxc.xcConfirm(phpdata.content, window.wxc.xcConfirm.typeEnum.warning);
						break;
					case 'error':
						window.wxc.xcConfirm(phpdata.content, window.wxc.xcConfirm.typeEnum.error);
						break;
					case 'success':
						window.wxc.xcConfirm(phpdata.content, window.wxc.xcConfirm.typeEnum.success);
						break;
				}
			}
}