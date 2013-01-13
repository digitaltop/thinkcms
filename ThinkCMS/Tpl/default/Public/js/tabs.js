function cur1(elfe){ 
$(elfe).addClass("act").siblings().removeClass("act");
}
function tab(id_tab,act){
	$(id_tab).find(".list_title a:first").addClass("act");
	$(id_tab).find(".list_tab").hide();
	$(id_tab).find(".list_tab:first").show();
if(!act){ act="click"};
if(act=="click"){
   $(id_tab).find(".list_title").find("a").each(function(i){
   $(id_tab).find(".list_title").find("a").eq(i).click(
   function(){
	$(id_tab).find(".list_title").find("a").removeClass("act");
	$(this).addClass("act");
    $(id_tab).find(".list_tab").hide();
	$(id_tab).find(".list_tab").eq(i).show();
    })           
   })  
   }
if(act=="mouseover"){
   $(id_tab).find(".list_title").find("a").each(function(i){
   $(id_tab).find(".list_title").find("a").eq(i).mouseover(
   function(){
	$(id_tab).find(".list_title").find("a").removeClass("act");
	$(this).addClass("act");
    $(id_tab).find(".list_tab").hide();
	$(id_tab).find(".list_tab").eq(i).show();
    })           
   })  
   } 
  
}
