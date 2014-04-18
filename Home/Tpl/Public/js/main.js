/**
 * Created by jack on 14-4-11.
 */
var jsonpHomeUrl = 'http://uniqlo.bigodata.com.cn/u2/index.php/Index';
var tmplPath = 'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/';
var baseurl='http://uniqlo.bigodata.com.cn/u2/';
//var jsonpHomeUrl = 'http://localhost/U2/index.php/Index';
//var tmplPath = 'http://localhost/U2/Home/Tpl/Public/';
//var baseurl='http://localhost/U2/';
var timer;
(function($, window, document,undefined) {

    var TmallUniqloHome = function() {
        this.$weather = weather;
        this.provi = '';
    }

    //定义Beautifier的方法
    TmallUniqloHome.prototype = {
        init: function() {
            var _this = this
            this.controlsEvent();
            this.cityOperator();

            var jsonpurl = baseurl +"index.php/Indexnew/getshopinfo?callback=callBackFunction.mapBindMarker";
            _this.$weather.jsonpFcuntion(jsonpurl);

            //天气初始化
            _this.$weather.init({
                'subindex':1,
                city : remote_ip_info.city || null,
                callback: function(city, temper, info){
                    var avg = callBackFunction.getavg(temper.high,temper.low);
                    _this.$weather.avg = avg;

                    var cookcity = _this.provi;
                    if(cookcity){
                        $.pron =  _this.provi;
                    }else{
                        $.pron = remote_ip_info.province;
                    }
                    _this.getSuits();
                }
            });

        },
        sendcity :function(pro,city){
            var url = "http://uniqlo.bigodata.com.cn/u1_5/mini.php/Sendcity/sendpro?callback=callBackFunction.jsonpCallbackm&proname="+pro+"&cityname="+city;
            // 创建script标签，设置其属性
            var script = document.createElement('script');
            script.setAttribute('src', url);
            // 把script标签加入head，此时调用开始
            document.getElementsByTagName('head')[0].appendChild(script);
        },


        //l4推荐模特图绑定
        getSuits :function(){
            var gender = $('#ulgender').find('.select').data('gender');
            this.showStyleMask(gender);
            //如果选中的是婴幼儿则灰掉后面的风格选项，然后显示婴幼儿的衣服
            if( gender == 4){
                $('#ul_index-bar-place').find('.select').removeClass('select');
                $('.ch_all').addClass('select');
                var url = jsonpHomeUrl +'/getgood?callback=callBackFunction.jsonpCallback3&tem='+this.$weather.avg+'&cid=0&sid=4&tid=0&pro='+$.pron;
                this.$weather.jsonpFcuntion(url);
                $('#suits-container').html('');
                $("#suits-container").hide();
                $('.id_content_banner_nav').hide();
            }
            else{
                var suitStyle = $('#ul_index-bar-place').find('.select').data('suitstyle');
                var jsonpurl = baseurl +'index.php/Indexnew/getConSuits?callback=callBackFunction.callbackSuits&tem='+this.$weather.avg +  '&sid='+gender+'&fid='+suitStyle;
                this.$weather.jsonpFcuntion(jsonpurl);
            }
        },
        showStyleMask : function(gender){
            if( gender == 4){
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').addClass('baby-style-mask').show();
//                $('.baby-style-mask').show();
            }
            else if(gender == 3){

                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').addClass('children-style-mask').show();
//                $('.baby-style-mask').hide();
//                $('.children-style-mask').show();
            }
            else if(gender == 2){
                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').addClass('male-style-mask').show();
//                $('.baby-style-mask').hide();
//                $('.children-style-mask').hide();
//                $('.male-style-mask').show();
            }
            else{
                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').hide();
//                $('.baby-style-mask').hide();
//                $('.children-style-mask').hide();
//                $('.male-style-mask').hide();
            }
        },

        easytabs : function(menunr, active) {
            if (menunr == autochangemenu) {
                currenttab = active;
            }
            if ((menunr == autochangemenu) && (stoponhover == 1)) {
                stop_autochange()
            } else if ((menunr == autochangemenu) && (stoponhover == 0)) {
                counter = 0;
            }
            menunr = menunr - 1;
            for (i = 1; i <= tabcount[menunr]; i++) {
                $('#'+tablink_idname[menunr] + i).removeClass('current');
                $('.'+tabcontent_idname[menunr] + i).css('display','none');
            }

            if($('#'+tablink_idname[menunr] + active)){
                $('#'+tablink_idname[menunr] + active).addClass('current');
                $('.'+tabcontent_idname[menunr] + active).css('display','block');
            }
        },
        controlsEvent : function(){
            var _this = this;
            //点击模特图跳转到虚拟试衣间并将相关衣服加入收藏夹中
            $('#suits-container').on('click','.imgSuits',function(){
                var $similarity = $(this).parent().find(".similarity");
                var numids = [];
                var list = $similarity.find("a");
                for(var i = 0;i<list.length;i++){
                    numids[i] = $(list[i]).data("numid");
                }
                //jsonpHomeUrl
                window.open( "http://uniqlo.bigodata.com.cn/u1_5/mini.php/Index/index/num/"+ numids.join());

//                var suitid = $(this).data('suitid');
//                var gender = $(this).data('gender');
//                //jsonpHomeUrl
//                window.open( "http://localhost/U2/mini.php/Index?suitid="+ suitid + "&gender=" + gender);
            });

            // 首页天气切换
            $('#ulweek').on('click', 'li', function(){
                var $that = $(this)
                $that.addClass('w_select').siblings('.w_select').removeClass('w_select');

                _this.$weather.init({
                    'subindex':1,
                    'shopid':_this.$weather.shopid,
                    index : $that.index() + 1,
                    city:$('#nio-city').text(),
                    imgpath : window.imgpath,
                    callback: function(city, temper, info){
                        var avg = callBackFunction.getavg(temper.high,temper.low);
                        _this.$weather.avg = avg;
                        avg = avg?avg:0;
                        _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                        _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                        _this.$weather.set = _this.$weather.set?_this.$weather.set:0;

                        _this.getSuits()
                        //往mimi传城市
                        _this.sendcity(city,info.city);
                    }
                });
            });

            $('#le1').on('change',function(){
                var pvalue = $('#le1 option:selected').val();
                var url = baseurl+"index.php/Indexnew/getcity?callback=callBackFunction.jsonpGetcity&pid="+pvalue;
                _this.$weather.jsonpFcuntion(url);
            });

            //地图城市切换
            $('#spid').on('change',function(){
                var pid = $('#spid option:selected').val();
                var url = baseurl+"index.php/Indexnew/getcity?callback=weather.jsonpBaiduCity&pid="+pid+"&baiduid=1";
                _this.$weather.jsonpFcuntion(url);
            });

            $('#scid').on('change',function(){
                H.map.centerAndZoom($("#scid option:selected").text(), 11);
                var pid = $('#spid option:selected').val(),cid = $('#scid option:selected').val();
                var cityname = $('#scid option:selected').text();
                if(cityname[cityname.length-1]=='市'){
                    var cnm = cityname.replace('市','');
                }else{
                    var cnm = $('#spid option:selected').text();
                }

                _this.$weather.init({'city' : cnm,imgpath : window.imgpath,'subid':'1','baiduerjiid':cid,
                    callback: function(city, temper, info){
                        var avg = callBackFunction.getavg(temper.high,temper.low);
                        _this.$weather.avg = avg;
                        avg = avg?avg:0;
                        _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                        _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                        _this.$weather.set = _this.$weather.set?_this.$weather.set:0;
                        _this.getSuits();
                        _this.sendcity(city,info.city);
                    }
                });
            });

            //点击性别更换模特图
            $('#ulgender').on('click','li',function(){
                var $this = $(this);
//                alert($(this).data('gender'));
                $('#ulgender').find('.select').removeClass('select');
                $this.addClass('select');
                _this.getSuits();
//               $('#ulgender').find('.select').data('gender');
            });

            $('#ul_index-bar-place').on('click','li a',function(){
                var $this = $(this);
                $('#ul_index-bar-place').find('.select').removeClass('select');
                $this.addClass('select');
                _this.getSuits();
            });

            $('.id_content_banner_nav_prev').on('click',function(){
                $('#suits-container').moveprev();
            })

            $('.id_content_banner_nav_next').on('click',function(){
                $('#suits-container').movenext();
            })


        },
        cityOperator : function(){
            var _this = this;
            $('#btn-city-close').on('click',this.hideCityDiv);

            $('#btn-city-change').on('click',function(){
                $('#div-citys').show();
            });

            $('#btn-change').on('click',function(){
                var $that = $(this),
                    province = $('#le1 option:selected').text()
                city = $('#le2 option:selected').text()
                temp = city.slice(-1);

                if(city !== '请选择'){
                    if(province !== '台湾省'){
                        if( province === '香港' || province === '澳门'){
                            city = province;
                        } else {
                            city = city.slice(0, (temp === '区' ? -2 : -1));
                        }
                    }
                    $('#nio-tip').text('正在加载天气数据，请稍等...').attr('title', '正在加载天气数据，请稍等...');
                    H.map.centerAndZoom(city, 11);
                    _this.$weather.init({'city' : city, 'province': province,imgpath : window.imgpath,'subindex':'1',
                        callback: function(city, temper, info){
                            var avg = callBackFunction.getavg(temper.high,temper.low);
                            $.weather.avg = avg;
                            avg = avg?avg:0;
                            _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                            _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                            _this.$weather.set = _this.$weather.set?_this.$weather.set:0;
                            _this.getSuits();
                            //往mimi传城市
                            sendcity(city,info.city);
                        }
                    });
                    _this.hideCityDiv();
                    $('#li_day0').addClass('w_select').siblings('.w_select').removeClass('w_select');

                } else alert('请选择城市！');
            });
        },

        hideCityDiv : function(){
            $('#div-citys').hide();
        }


    }//prototype

    var tmHome = new TmallUniqloHome();
    tmHome.init();

})(jQuery, window, document);


var callBackFunction = {

    callbackSuits : function(list){
        $("#div_index-bin,.index-suit").hide();
        if(list == null){
//            var jsonpurl = baseurl +"index.php/Indexnew/getSuits?callback=callBackFunction.callbackSuits&tem="+weather.avg;
//            weather.jsonpFcuntion(jsonpurl);
            $("#suits-container").hide();
            return;
        }

        var strHtml = "";
        var listlength = list.length;
//        if(listlength > 6){
//            listlength = 6
//        }

        for(var i = 0 ;i < listlength;i++){
            strHtml += this.getCoverScrollItem(list[i]);
        }
        $('#suits-container').html(strHtml);
        $("#suits-container").show();
        if( list.length > 6 ){
            $('.id_content_banner_nav').show();
        }
        else{
            $('.id_content_banner_nav').hide();
        }
        $('#suits-container').coverscroll({items:'.item',minfactor:15,  'step':{ // compressed items on the side are steps
            'begin':0,//first shown step
            'limit':6, // how many steps should be shown on each side
            'width':8, // how wide is the visible section of the step in pixels
            'scale':true // scale down steps
        }});
    },
    jsonpGetcity : function(data){
        var str = '<option value="0">请选择</option>';
        $.each(data.clist,function(pin,pv){
            if(pv.sel==1){
                var sel2 = "selected='selected'";
            }
            str+="<option value='"+pv.region_id+"' "+sel2+">"+pv.local_name+"</option>";
        });
        $('#le2').html(str);
    },
    jsonpCallbackm :function(data){

    },
    getCoverScrollItem : function(item){
        var strItem = '<div class="item">';
        strItem += '<img class="imgSuits" src="'+ item.suitImageUrl +'" data-gender="'+ item.suitGenderID+'"  data-suitid="'+ item.suitID +'" />';
        strItem += '<div class="similarity">';
        var detail = item.detail;
        var numids = [];
        if(detail != null){
            for(var i =0;i<detail.length;i++){
                numids[i] = detail[i].num_iid;
                strItem += '<div class="circle">'
                strItem += '<a data-numid="'+detail[i].num_iid +'" href="'+ detail[i].detail_url +'" target="_blank">';
                strItem += '<img src="http://uniqlo.bigodata.com.cn/'+   detail[i].pic_url +'" ></a></div>';
            }
        }
        strItem +='</div>';
        strItem += '<div class="itemTitle">'+item.description+'</div>';//<br><font style="color: #C0C0C0">'+ item.eglishName+'</font>
        strItem += '<div class="gotoroom none">';
        strItem += '<a href="http://uniqlo.bigodata.com.cn/u1_5/mini.php/Index/index/num/'+ numids.join() +'" target="_blank">去虚拟试衣间试穿</a></div></div>';
        return strItem;
    },
    getavg :function(high,low){
        var avg = Math.ceil((parseInt(low)+parseInt(high))/2);
        return avg;
    },
    tipsfunction : function(v,k){
        //tips
        stop_autochange();
        if(k){
            for (i = 1; i <=3; i++) {
                $('#tablink' + i).removeClass('current');
                $('.preferential_' + i).css('display','none');
            }
            $('#tablink2').addClass('current');
            $('.preferential_2').css('display','block');
        }
        $('#shopid').html(v);
    },
    //将店铺信息添加到地图中
    mapBindMarker : function (data){
        H.initData(data);
    },
    jsonpCallback3 : function(da){
        if(da.flag1=='p'){
                //如果是婴幼儿走这里
                if(da.sid==4){
                    if(da.fl==1){
                        $('#upc').html(da.ustr);
                        $('#downc').html(da.dstr);
                    }else{
                        $('#taoz').html(da.ustr);
                    }
                }else{
                    $('#upc').html(da.ustr);
                    $('#downc').html(da.dstr);
                }
        }

        if(da.fl==1){
            if(da.sid==4){
                $('.index-single').removeClass('none');
                $('.index-suit').addClass('none');
                $('.index-suit').css('display','none');
                $('.index-single').css('display','block');
            }
            $('#tishi').css('display','none');
            $('#qtm').removeClass('none');
        }else{
            if(da.sid==4){
                $('.index-single').addClass('none');
                $('.index-suit').removeClass('none');
                $('.index-suit').css('display','block');
                $('.index-single').css('display','none');
            }else{
                $('.index-single').removeClass('none');
                $('.index-suit').addClass('none');
                $('.index-suit').css('display','none');
                $('.index-single').css('display','block');
            }
            $('#tishi').css('display','none');
            $('#qtm').addClass('none');
        }
        $.uniqlo.kvSlider();
    }
};


var tablink_idname = new Array("tablink");
var tabcontent_idname = new Array("preferential_");
var tabcount = new Array("3");
var loadtabs = new Array("1");
var autochangemenu = 1,counter = 0,slength;
var changespeed = 1;
var stoponhover = 0;
var menucount = loadtabs.length;
var a = 0;
var b = 1;

function easytabs(menunr, active) {
    if (menunr == autochangemenu) {
        currenttab = active;
    }
    if ((menunr == autochangemenu) && (stoponhover == 1)) {
        stop_autochange()
    } else if ((menunr == autochangemenu) && (stoponhover == 0)) {
        counter = 0;
    }
    menunr = menunr - 1;
    for (i = 1; i <= tabcount[menunr]; i++) {
        $('#'+tablink_idname[menunr] + i).removeClass('current');
        $('.'+tabcontent_idname[menunr] + i).css('display','none');
    }

    if($('#'+tablink_idname[menunr] + active)){
        $('#'+tablink_idname[menunr] + active).addClass('current');
        $('.'+tabcontent_idname[menunr] + active).css('display','block');
    }
}


var totaltabs = tabcount[autochangemenu - 1];
var currenttab = loadtabs[autochangemenu - 1];

function start_autochange() {
    counter = counter + 1;
    timer = setTimeout("start_autochange()", 3000);
    if (counter == changespeed + 1) {
        currenttab++;
        if (currenttab > totaltabs) {
            currenttab = slength;
        }
        easytabs(autochangemenu, currenttab);
        //restart_autochange();
    }
}

function restart_autochange() {
    clearTimeout(timer);
    counter = 0;
    start_autochange();
}
function stop_autochange() {
    clearTimeout(timer);
    counter = 0;
}