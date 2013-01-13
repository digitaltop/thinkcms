//顶部
$("a.top_nav_more").hover(function(){
		$(this).find(".top_nav_cover").show();
	})
	$(".top_nav_cover").hover(function(){
		$(this).show();
	},function(){
		$(this).hide();
	})

//菜单
$(".nav_li").hover(function(){
			$(".fl_nav_hover").hide();
			$(this).find(".fl_nav_hover").show();
		},function(){
			$(".fl_nav_hover").hide();
		})

//焦点图片
$(function(){
						 var len  = $(".num > li").length;
						 var index = 0;
						 var adTimer;
						 $(".num li").mouseover(function(){
							index  =   $(".num li").index(this);
							showImg(index);
						 }).eq(0).mouseover();	
						 
						 $('#topnews_pic').hover(function(){
								 clearInterval(adTimer);
							 },function(){
								 adTimer = setInterval(function(){
									index++;
									if(index==len){index=0;}
									showImg(index);	
								  } , 5000);
						 }).trigger("mouseleave");
					})
					
					function showImg(index){
							var adHeight = $("#topnews_pic").height();
							$(".slider").stop(true,false).animate({top : -adHeight*index},500);
							$(".num li").removeClass("on")
								.eq(index).addClass("on");
					}