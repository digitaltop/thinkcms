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