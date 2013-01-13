function getClientDate(){
    var today=new Date();
    var y,m,d,w,t;
    y=today.getFullYear();
    m=today.getMonth()+1;
    d=today.getDate();

    w=today.getDay();
    if(today.getDay()==0) w = "星期日";
    if(today.getDay()==1) w = "星期一";
    if(today.getDay()==2) w = "星期二";
    if(today.getDay()==3) w = "星期三";
    if(today.getDay()==4) w= "星期四";
    if(today.getDay()==5) w = "星期五";
    if(today.getDay()==6) w = "星期六";

    $('#JsclientDate').html(y+"年"+m+"月"+d+"日 "+w+"");
}
//导航菜单
$(".lmenu_list").hover(function(){
    $(".lmenu_covre").hide();
    $(this).find(".lmenu_covre").show();
},function(){
    $(".lmenu_covre").hide();
})
//焦点图片内容
$(function(){
    var len  = $(".num1 > li").length;
    var index = 0;
    var adTimer;
    $(".num1 li").mouseover(function(){
        index  =   $(".num1 li").index(this);
        showImg(index);
    }).eq(0).mouseover();	
							 
    $('#i_picnews_pic').hover(function(){
        clearInterval(adTimer);
    },function(){
        adTimer = setInterval(function(){
            index++;
            if(index==len){
                index=0;
            }
            showImg(index);	
        } , 5000);
    }).trigger("mouseleave");
})
						
function showImg(index){
    var adHeight = $("#i_picnews_pic").height();
    $(".slider1").stop(true,false).animate({
        top : -adHeight*index
        },500);
    $(".num1 li").removeClass("on")
    .eq(index).addClass("on");
}

//时间
getClientDate();
//标签切换
tab("#tab1","mouseover");
tab("#tab2","mouseover");
tab("#tab3","mouseover");
tab("#tab4","mouseover");
tab("#tab5","mouseover");
tab("#tab6","mouseover");
tab("#tab7","mouseover");
tab("#tab8","mouseover");
tab("#tab9","mouseover");
tab("#tab10","mouseover");
tab("#tab11","mouseover");	
		
//AD标签
function r_AD_cur1(elfe){ 
    $(elfe).addClass("act").siblings().removeClass("act");
}
function r_AD_tab(r_AD_id_tab){
    $(r_AD_id_tab).find(".tab_title a:first").addClass("act");
    $(r_AD_id_tab).find(".tab_list").hide();
    $(r_AD_id_tab).find(".tab_list:first").show();
						
    $(r_AD_id_tab).find(".tab_title").find("a").each(function(i){
        $(r_AD_id_tab).find(".tab_title").find("a").eq(i).mouseover(
            function(){
                $(r_AD_id_tab).find(".tab_title").find("a").removeClass("act");
                $(this).addClass("act");
                $(r_AD_id_tab).find(".tab_list").hide();
                $(r_AD_id_tab).find(".tab_list").eq(i).show();
            })           
    })  
}
r_AD_tab("#r_ad_tab1");
