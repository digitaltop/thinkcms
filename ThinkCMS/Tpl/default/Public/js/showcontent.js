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

function cur1(elfe){ 
						$(elfe).addClass("act").siblings().removeClass("act");
						}
						function tab(id_tab){
							$(id_tab).find(".view_fl_title a:first").addClass("act");
							$(id_tab).find(".viewflcon_list").hide();
							$(id_tab).find(".viewflcon_list:first").show();
						
						   $(id_tab).find(".view_fl_title").find("a").each(function(i){
						   $(id_tab).find(".view_fl_title").find("a").eq(i).mouseover(
						   function(){
							$(id_tab).find(".view_fl_title").find("a").removeClass("act");
							$(this).addClass("act");
							$(id_tab).find(".viewflcon_list").hide();
							$(id_tab).find(".viewflcon_list").eq(i).show();
							})           
						   })  
						   }
					tab("#view_fl_1");