<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <html xmlns:wb="http://open.weibo.com/wb">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php if(isset($PAGESEO['title']) && !empty($PAGESEO['title'])): echo ($PAGESEO['title']); endif; if(isset($PAGESEO['cate_title']) && !empty($PAGESEO['cate_title'])): echo ($PAGESEO[cate_title]); endif; echo ($SEO['site_title']); ?></title>
            <meta name="keywords" content="<?php if(isset($PAGESEO['keywords']) && !empty($PAGESEO['keywords'])): echo ($PAGESEO['keywords']); else: echo ($SEO['keywords']); endif; ?>">
                <meta name="description" content="<?php if(isset($PAGESEO['description']) && !empty($PAGESEO['description'])): echo ($PAGESEO['description']); else: echo ($SEO['description']); endif; ?>">
                    <script src="<?php echo C('JS_PATH');?>jquery-1.4.2.min.js"></script>
                    <script src="<?php echo C('JS_PATH');?>tabs.js"></script>
                    <script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=67225079" type="text/javascript" charset="utf-8"></script>
                    <link href="<?php echo C('CSS_PATH');?>main.css" rel="stylesheet" type="text/css" />
                    </head>
                    <body>
                        <div class="main_div">
                            <?php echo getAd('ad_index_top');?>
                            <div class="header">
                                <div class="logo"><a href="<?php echo C('BASE_URL');?>" title="<?php echo C('APP_TITLE');?>"><img src="<?php echo C('IMG_PATH');?>logo.png" style="margin-top:16px;" alt="<?php echo C('APP_TITLE');?>" /></a></div>
                                <div class="top_search">
                                    <div class="t_s_hotkey"><?php if(is_array($topSearch)): $i = 0; $__LIST__ = $topSearch;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["catname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?></div>
                                    <div class="t_s_con"><form action="<?php echo U('Index/search');?>" method="get" target="_blank"><input name="" type="text"  class="top_saerch_inp" autocomplete="off" x-webkit-grammar="builtin:translate" x-webkit-speech="" /><input type="submit" class="top_search_sub" value="  " />
                                        </form></div>
                                </div>
                                <div class="top_ad">
                                    <div class="top_ad_con" id="460_60_1"><?php echo getAd('ad_index_460_60_1');?></div>
                                </div>
                                <div class="clr"></div>
                            </div>
                            <div class="index_con">
                                <div class="index_left">
                                    <div class="i_l_menu">
                                        <div class="i_l_menu_time" id="JsclientDate"></div>
                                        <?php if(is_array($treeMenu)): $i = 0; $__LIST__ = $treeMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="lmenu_list">
                                                <div class="lmenu_covre">
                                                    <div class="lmenu_cover_left">
                                                        <div class="lmenu_cover_left_cover"><a<?php if(($vo['css']) != ""): ?>class="<?php echo ($vo["css"]); ?>"<?php endif; ?> href="<?php echo ($vo["url"]); ?>" target="_blank"<?php echo (title_style($vo["style"])); ?>><?php echo ($vo["catname"]); ?></a></div>
                                                    </div>
                                                    <div class="lmenu_cover_right">
                                                        <ul><?php if(is_array($vo["childTree"])): $i = 0; $__LIST__ = $vo["childTree"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a<?php if(($sub['css']) != ""): ?>class="<?php echo ($sub["css"]); ?>"<?php endif; ?> href="<?php echo ($sub["url"]); ?>" target="_blank"<?php echo (title_style($sub["style"])); ?>><?php echo ($sub["catname"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                                        </ul>
                                                    </div>
                                                    <div class="clr"></div>
                                                </div>
                                                <div style="background-color:#<?php echo ($vo["ico"]); ?>" class="fd_icon"><?php echo ($vo["shortname"]); ?></div><a<?php if(($vo['css']) != ""): ?>class="<?php echo ($vo["css"]); ?>"<?php endif; ?> href="<?php echo ($vo["url"]); ?>" target="_blank"<?php echo (title_style($vo["style"])); ?>><?php echo ($vo["catname"]); ?></a>
                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <div class="lmenu_list">
                                            <div class="lmenu_covre">
                                                <div class="lmenu_cover_left">
                                                    <div class="lmenu_cover_left_cover"><a href="http://jb.kx168.cn/" target="_blank">疾病板块</a></div>
                                                </div>						
                                                <div class="clr"></div>
                                            </div>
                                            <div style="background-color:#0d5cb5" class="fd_icon">病</div><a href="http://jb.kx168.cn/" target="_blank">疾病板块</a>
                                        </div><div class="lmenu_list">
                                            <div class="lmenu_covre">
                                                <div class="lmenu_cover_left">
                                                    <div class="lmenu_cover_left_cover"><a href="http://sh.kx168.cn/" target="_blank">生活健康板块</a></div>
                                                </div>						
                                                <div class="clr"></div>
                                            </div>
                                            <div style="background-color:#78b50d" class="fd_icon">健</div><a href="http://sh.kx168.cn/" target="_blank">生活健康板块</a>
                                        </div>
                                    </div>
                                    <div class="i_l_ad" id="160_600_1" style="height:302px; overflow:hidden"><?php echo getAd('ad_index_160_600_1');?></div>
                                    <div class="i_l_ad" id="160_302_1" style="height:302px; overflow:hidden"><div class="tab" id="r_ad_tab1">
                                            <div class="tab_title"><a href="http://test.kx168.cn/">健康自测&nbsp;</a><a href="http://zzk.kx168.cn/">&nbsp;健康自诊</a></div>
                                            <div class="tab_con">
                                                <div class="tab_list">
                                                    <a href="http://test.39.net/test/5721.html" class="test1">BMI测试<br /><span class="gray">测您的标准体重</span></a>                	<a href="http://test.39.net/test/5712.html" class="test3">乙肝自测<br /><span class="gray">用化验结果自测</span></a>
                                                    <a href="http://test.39.net/test/5719.html" class="test2">安全期自测<br /><span class="gray">女性安全期推算</span></a>
                                                    <a href="http://test.39.net/test/5435.html" class="test4">智商测试<br /><span class="gray">测您的IQ有多高</span></a>
                                                    <ul>
                                                        <?php echo getAd('ad_index_jkTest_text_1');?>
                                                    </ul>
                                                </div>
                                                <div class="tab_list">
                                                    <ul class="bodybg">
                                                        <li class="zz1"><a href="http://zzk.39.net/bw/toubu.html" title="头部症状">头部</a></li>
                                                        <li class="zz2"><a href="http://zzk.39.net/bw/yan.html" title="眼部症状">眼部</a></li>
                                                        <li class="zz3"><a href="http://zzk.39.net/bw/er.html" title="耳部症状">耳部</a></li>
                                                        <li class="zz4"><a href="http://zzk.39.net/bw/kou.html" title="口腔症状">口腔</a></li>
                                                        <li class="zz5"><a href="http://zzk.39.net/bw/bi.html" title="鼻子症状">鼻子</a></li>
                                                        <li class="zz6"><a href="http://zzk.39.net/bw/jingbu.html" title="颈部症状">颈部</a></li>
                                                        <li class="zz7"><a href="http://zzk.39.net/bw/xiongbu.html" title="胸部症状">胸部</a></li>
                                                        <li class="zz8"><a href="http://zzk.39.net/bw/fubu.html" title="腹部症状">腹部</a></li>
                                                        <li class="zz9"><a href="http://zzk.39.net/bw/sizhi.html" title="上肢症状">上肢</a></li>
                                                        <li class="zz10"><a href="http://zzk.39.net/bw/pifu.html" title="皮肤症状">皮肤</a></li>
                                                        <li class="zz11"><a href="http://zzk.39.net/bw/shengzhibuwei.html" title="生殖部位症状">生殖部位</a></li>
                                                        <li class="zz12"><a href="http://zzk.39.net/bw/sizhi.html" title="下肢症状">下肢</a></li>
                                                        <li class="zz13"><a href="http://zzk.39.net/bw/quanshen.html">全身症状</a></li>
                                                    </ul>
                                                    <ul>
                                                        <?php echo getAd('ad_index_jkTest_text_2');?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div></div>
                                    <div class="i_l_ad" id="138_292_1" style="height:302px; overflow:hidden"><div style="padding:9px 0 0 9px;"><?php echo getAd('ad_index_138_292_1');?></div></div>
                                    <div class="i_l_ad" id="160_600_2" style="height:302px; overflow:hidden"><?php echo getAd('ad_index_160_600_4');?></div>
                                    <div class="i_l_ad" id="160_600_5" style="height:302px; overflow:hidden"><?php echo getAd('ad_index_160_600_5');?></div>
                                </div>
                                <div class="index_right">
                                    <div class="i_r_1">
                                        <div class="i_picnews">
                                            <!--焦点图片内容 -->
                                            <div class="i_picnews_pic" id="i_picnews_pic">
                                                <ul class="slider1" >
                                                    <?php if(is_array($positionPic)): $i = 0; $__LIST__ = $positionPic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><div class="licover"><a href="<?php echo ($vo["url"]); ?>" target="_blank"<?php echo (title_style($vo["style"])); ?> editor="topimg" title="<?php echo ($vo["description"]); ?>"><?php echo (str_cut($vo["description"],99,'...')); ?></a></div><a href="<?php echo ($vo["url"]); ?>" target="_blank"<?php echo (title_style($vo["style"])); ?> title="<?php echo ($vo["title"]); ?>"><img src="<?php echo (thumb($vo["thumb"],497,298)); ?>" alt="<?php echo ($vo["title"]); ?>" width="497" height="298" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                                </ul>
                                            </div>					
                                            <ul class="num1" >
                                                <?php if(is_array($positionPic)): $i = 0; $__LIST__ = $positionPic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><div class="ip_li_pic"><img src="<?php echo (thumb($vo["thumb"],80,33)); ?>" width="80" height="33" alt="<?php echo ($vo["title"]); ?>" /></div><div class="ip_li_text"><a target="_blank" href="<?php echo ($vo["url"]); ?>"<?php echo (title_style($vo["style"])); ?> editor="subimg"><?php echo (str_cut($vo["title"],45)); ?></a></div></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </ul>
                                            <!--焦点图片内容 -->
                                        </div>
                                        <div class="i_weibo">
                                            <div class="i_weibo_title"><span><wb:follow-button uid="2884465240" type="red_2" width="136" height="24" ></wb:follow-button></span><a href="http://www.weibo.com/u/2884465240" target="_blank" hidefocus>健康微博直击</a></div>
                                            <div class="i_weibo_con"><wb:livestream color="C1CFDD,f0f0f0,444444,5093d5,ffffff,F4F9FF" titlebar="n" member="n" topic="%E5%BC%80%E5%BF%83~%E5%81%A5%E5%BA%B7~%E7%96%BE%E7%97%85~%E7%94%9F%E6%B4%BB|%E5%BC%80%E5%BF%83~%E5%81%A5%E5%BA%B7~%E7%96%BE%E7%97%85~%E7%94%9F%E6%B4%BB" width="auto" height="377" ></wb:livestream></div>
                                        </div>
                                        <div class="clr"></div>
                                    </div>
                                    <div class="i_r_1">
                                        <div class="i_r_list">
                                            <div class="list_main" id="tab1">
                                                <div class="list_title"><a>健康头条</a><a>健康排行</a><a>病症相关</a><a>生活保健</a><a>疯狂贴图</a></div>
                                                <div class="list_con">
                                                    <!--健康头条-->
                                                    <div class="list_tab"><?php echo ($positionTop); ?></div>
                                                    <!--健康排行-->
                                                    <div class="list_tab"><?php echo ($topList); ?></div><div class=clr></div></div>
                                                <!--病症相关-->
                                                <div class="list_tab"><?php echo ($positionJiBing); ?></div>
                                                <!--生活保健-->
                                                <div class="list_tab"><?php echo ($positionBaoJian); ?></div>
                                                <!--疯狂贴图-->
                                                <div class="list_tab"><?php if(is_array($positionTieTu)): $k = 0; $__LIST__ = $positionTieTu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1): ?><div class="fktt_hot">
                                                                <div class="fktt_hot_pic"><img alt="<?php echo ($vo["title"]); ?>" src="<?php echo (thumb($vo["thumb"], 75, 32)); ?>" width="75" height="32"></div>
                                                                <div class="fktt_hot_text"><a target="_blank" href="<?php echo ($vo["url"]); ?>" class="t"<?php echo (title_style($vo["style"])); ?> title="<?php echo ($vo["title"]); ?>"><?php echo (str_cut(trim($vo["title"]),90)); ?></a><br /><?php echo (str_cut(trim($vo["description"]),105)); ?></div>
                                                                <div class="clr"></div>
                                                            </div>
                                                            <?php else: ?>
                                                            <div class="fktt">
                                                                <div class="fktt_pic"><a target="_blank" href="<?php echo ($vo["url"]); ?>"<?php echo (title_style($vo["style"])); ?> title="<?php echo ($vo["title"]); ?>"><img alt="<?php echo ($vo["title"]); ?>" src="<?php echo (thumb($vo["thumb"], 124, 84)); ?>"></a></div>
                                                                <div class="fktt_text"><a target="_blank" href="<?php echo ($vo["url"]); ?>"<?php echo (title_style($vo["style"])); ?> title="<?php echo ($vo["title"]); ?>"><?php echo (str_cut(trim($vo["title"]),30,'')); ?></a></div>								
                                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                    <div class="clr"></div></div></div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab2">
                                            <div class="list_title"><a href="http://sex.kx168.cn/" target="_blank">两性与爱</a><a href="http://jiankangtupu.kx168.cn/" target="_blank">健康图谱</a></div>
                                            <div class="list_title_span" id="tab2_text"><?php echo getAd('ad_index_tab2_text');?></div>
                                            <div class="list_con">
                                                <!--两性与爱-->
                                                <div class="list_tab"><?php echo ($news[1458]); ?></div>
                                                <!--健康图谱-->
                                                <div class="list_tab"><?php echo ($news[1768]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab4">
                                            <div class="list_title">合作广告<em>&gt;&gt;</em></div>
                                            <div class="list_title_span" id="tab4_text"><?php echo getAd('ad_index_tab4_text');?></div>
                                            <div class="list_con" id="412_280_1"><?php echo getAd('ad_index_412_280_1');?></div>
                                        </div>
                                    </div>
                                    <div class="clr"></div>				
                                    <div class="i_ad_tl" id="834_80_1"><?php echo getAd('ad_index_834_80_1');?></div>
                                </div>
                                <div class="i_r_1">
                                    <div class="i_r_list">
                                        <div class="list_main" id="tab3">
                                            <div class="list_title"><a href="http://fuke.kx168.cn/" target="_blank">妇科疾病</a><a href="http://nanke.kx168.cn/" target="_blank">男科疾病</a></div>
                                            <div class="list_title_span" id="tab3_text"><?php echo getAd('ad_index_tab3_text');?></div>
                                            <div class="list_con"><!--妇科疾病-->
                                                <div class="list_tab"><?php echo ($news[22]); ?></div>
                                                <!--男科疾病-->
                                                <div class="list_tab"><?php echo ($news[496]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab5">
                                            <div class="list_title"><a href="http://baojian.kx168.cn/nvxingbaojian/" target="_blank">女性保健</a><a href="http://baojian.kx168.cn/nanxingbaojian/" target="_blank">男性保健</a></div>
                                            <div class="list_title_span" id="tab5_text"><?php echo getAd('ad_index_tab5_text');?></div>
                                            <div class="list_con">
                                                <!--女性保健-->
                                                <div class="list_tab"><?php echo ($news[1606]); ?></div>
                                                <!--男性保健--><div class="list_tab"><?php echo ($news[1607]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list">
                                        <div class="list_main" id="tab6">
                                            <div class="list_title"><a href="http://tangniaobing.kx168.cn/" target="_blank">糖尿病</a><a href="http://zhongliu.kx168.cn/" target="_blank">肿瘤</a></div>
                                            <div class="list_title_span" id="tab6_text"><?php echo getAd('ad_index_tab6_text');?></div>
                                            <div class="list_con">
                                                <!--糖尿病-->
                                                <div class="list_tab"><?php echo ($news[217]); ?></div>
                                                <!--肿瘤-->
                                                <div class="list_tab"><?php echo ($news[421]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab7">
                                            <div class="list_title"><a href="http://baojian.kx168.cn/yingyouerbaojain/" target="_blank">婴幼儿保健</a><a href="http://baojian.kx168.cn/laonainren/" target="_blank">老年人保健</a></div>
                                            <div class="list_title_span" id="tab7_text"><?php echo getAd('ad_index_tab7_text');?></div>
                                            <div class="list_con">
                                                <!--婴幼儿保健-->
                                                <div class="list_tab"><?php echo ($news[1604]); ?></div>
                                                <!--老年人保健--><div class="list_tab"><?php echo ($news[1610]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_ad_tl" id="834_80_2"><?php echo getAd('ad_index_834_80_2');?></div>
                                
                                <!--    -->
                                <div class="i_r_1">
                                    <div class="i_r_list">
                                        <div class="list_main" id="tab8">
                                            <div class="list_title"><a href="http://zhongyi.kx168.cn/" target="_blank">中医</a><a href="http://pianfang.kx168.cn/" target="_blank">偏方</a></div>
                                            <div class="list_title_span" id="tab8_text"><?php echo getAd('ad_index_tab8_text');?></div>
                                            <div class="list_con">
                                                <!--中医-->
                                                <div class="list_tab"><?php echo ($news[935]); ?></div>
                                                <!--偏方--><div class="list_tab"><?php echo ($news[841]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab9">
                                            <div class="list_title"><a href="http://muying.kx168.cn/" target="_blank">母婴</a><a href="http://xinli.kx168.cn/" target="_blank">心理</a></div>
                                            <div class="list_title_span" id="tab9_text"><?php echo getAd('ad_index_tab9_text');?></div>
                                            <div class="list_con">
                                                <!--母婴-->
                                                <div class="list_tab"><?php echo ($news[1316]); ?></div>
                                                <!--心理--><div class="list_tab"><?php echo ($news[1223]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list">
                                        <div class="list_main" id="tab10">
                                            <div class="list_title"><a href="http://buyunbuyu.kx168.cn/" target="_blank">不孕不育</a><a href="http://ganbing.kx168.cn/" target="_blank">肝病</a></div>
                                            <div class="list_title_span" id="tab10_text"><?php echo getAd('ad_index_tab10_text');?></div>
                                            <div class="list_con">
                                                <!--不孕不育-->
                                                <div class="list_tab"><?php echo ($news[6]); ?></div>
                                                <!--肝病-->
                                                <div class="list_tab"><?php echo ($news[92]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="i_r_list pl6px">
                                        <div class="list_main" id="tab11">
                                            <div class="list_title"><a href="http://bagua.kx168.cn/" target="_blank">八卦</a><a href="http://yinshi.kx168.cn/" target="_blank">饮食</a></div>
                                            <div class="list_title_span" id="tab11_text"><?php echo getAd('ad_index_tab11_text');?></div>
                                            <div class="list_con">
                                                <!--八卦-->
                                                <div class="list_tab"><?php echo ($news[1915]); ?></div>
                                                <!--饮食--><div class="list_tab"><?php echo ($news[1644]); ?></div>
                                            </div>
                                        </div>
                                    </div>			
                                    <div class="i_ad_tl" id="834_80_3"><?php echo getAd('ad_index_834_80_3');?></div>
                                </div>
                            </div>
                            <div class="clr"></div>
                        </div>
                        <div class="footer"><?php if(is_array($footerNav)): $i = 0; $__LIST__ = $footerNav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo["url"]); ?>" target="_blank"><?php echo ($vo["catname"]); ?></a> |<?php endforeach; endif; else: echo "" ;endif; ?> 警方提示防诈骗、远离黄赌毒<br />
开心健康平台 - <a href="<?php echo C('BASE_URL');?>" target="_blank"><?php echo C('APP_TITLE');?></a> | <a href="http://so.kx168.cn/" target="_blank">健康搜</a> | <a href="http://zhidao.kx168.cn/" target="_blank">健康知道</a> | <a href="http://www.cc120.cn/" target="_blank">网络疾病数据库</a> | <a href="http://forum.kx168.cn/" target="_blank">健康论坛</a><br />
Copyright  ©2003-<?php echo(date('Y')); ?>　<?php echo C('BASE_URL');?>　版权所有 成都阿美科技有限公司 蜀ICP备05004007号 <br />
特别声明：本站信息仅供参考　不能作为诊断及医疗的依据　本站如有转载或引用文章涉及版权问题请速与我们联系<br />
<div class="footernew footheight">
	<div style="width:1000px;">
	<p>
		<span class="fl"><a href="http://www.cdnet110.com/" target="_blank" rel="nofollow"><img width="36" height="43" border="0" alt="成都网络警察报警平台" src="<?php echo C('IMG_PATH');?>816587.gif"></a></span>
		<span class="fr"><a href="http://www.cdnet110.com/" target="_blank" rel="nofollow">成都网络警<br>
		察报警平台</a></span>
	</p>
	<p>
		<span class="fl"><a href="#" target="_blank" rel="nofollow"><img width="36" height="42" border="0" alt="公共信息安全网络监察" src="<?php echo C('IMG_PATH');?>6743671.jpg"></a></span>
		<span class="fr"><a href="#" target="_blank" rel="nofollow">公共信息安<br>
		全网络监察</a></span>
	</p>
	
	<p>
		<span class="fl"><a href="http://www.miibeian.gov.cn" target="_blank" rel="nofollow"><img width="35" height="43" border="0" alt="非经营性网站备案信息" src="<?php echo C('IMG_PATH');?>ind36.gif"></a></span>
		<span class="fr"><a target="_blank" class="lcblack" href="http://www.miibeian.gov.cn" rel="nofollow">非经营性网站<br>
		备案信息</a></span>
	</p>
	<p>
		<span style="width:44px;" class="fl"><a href="http://net.china.cn/chinese/index.htm" target="_blank" rel="nofollow"><img width="44" height="44" border="0" alt="不良信息举报中心" src="<?php echo C('IMG_PATH');?>home_b.gif"></a></span>
		<span style="width:64px;" class="fr"><a class="lcblack" href="http://net.china.cn/chinese/index.htm" target="_blank" rel="nofollow">不良信息<br>
		举报中心</a></span>
	</p>
	<p>
		<span style="width:44px;" class="fl"><a href="http://www.wenming.cn" target="_blank" rel="nofollow"><img width="44" height="42" border="0" alt="中国文明网传播文明" src="<?php echo C('IMG_PATH');?>wmlogo.gif"></a></span>
		<span style="width:64px;" class="fr"><a class="lcblack" href="http://www.wenming.cn" target="_blank" rel="nofollow">中国文明网<br>传播文明</a></span>
	</p>
	<p style="width:141px;">
		<span style="padding:0;" class="fl"><a href="http://www.cxwz.org/" target="_blank" rel="nofollow"><img border="0" alt="诚信网站" src="<?php echo C('IMG_PATH');?>cxrz3.png"></a></span>
	</p>
	<p style="width:141px;">
		<span style="padding:0;" class="fl"><a href="http://trust.360.cn/search.php" target="_blank" title="360绿色网站"><img src="<?php echo C('IMG_PATH');?>logo_360.png" border="0" /></a></span>
	</p>
	</div>	
</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fedd31860d16a5064bc054a2b079183a1' type='text/javascript'%3E%3C/script%3E"));
</script>
                        </div>
                        <?php echo getAd('ad_index_couplet');?>
                        <script src="http://js.kx168.cn/index.js"></script>
                    </body>
                    </html>