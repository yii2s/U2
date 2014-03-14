/**
 * Created with JetBrains WebStorm.
 * User: Mos,jack.wu
 * Date: 13-5-30
 * Time: 上午11:40
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

    var weather = {
        currentOption : "",
        tips : [
            '请注意防暑降温，宜穿短袖、背心、短裙、短裤、薄型T恤',
            '午时避免在户外久留，穿短裙/裤、短套装、T恤、长绒棉短袖',
            '薄型棉杉+牛仔裤/休闲裤，或针织连衣裙是不错的选择',
            '白天穿长袖衬衫+薄型套装，配牛仔裤，晚上加件针织衫吧',
            '美利奴毛衣、混纺/羊毛/羊绒衫、风衣、连帽茄克赶紧穿起来',
            '羽绒服或羊毛混纺短大衣，内配精纺美利奴毛衣+围巾',
            '宜穿厚羽绒服、摇粒绒外套+羽绒背心，配上帽子和手套'
        ],
        time : function(){
            var
                now = new Date(),
                year = now.getFullYear(),
                month = now.getMonth(),
                date = now.getDate(),
                day = now.getDay(),
                arr = ['周日','周一','周二','周三','周四','周五','周六'],
                second = new Date(year, month, date + 1),
                third  = new Date(year, month, date + 2),
                forth  = new Date(year, month, date + 3),
                fifth  = new Date(year, month, date + 4),
                sixth  = new Date(year, month, date + 5);

            return [
                {
                    day : arr[day],
                    year: year,
                    month: month + 1,
                    date: date
                },
                {
                    day : arr[second.getDay()],
                    year: second.getFullYear(),
                    month:second.getMonth() + 1,
                    date: second.getDate()
                },
                {
                    day : arr[third.getDay()],
                    year: third.getFullYear(),
                    month:third.getMonth() + 1,
                    date: third.getDate()
                },
                {
                    day : arr[forth.getDay()],
                    year: forth.getFullYear(),
                    month:forth.getMonth() + 1,
                    date: forth.getDate()
                },
                {
                    day : arr[fifth.getDay()],
                    year: fifth.getFullYear(),
                    month:fifth.getMonth() + 1,
                    date: fifth.getDate()
                },
                {
                    day : arr[sixth.getDay()],
                    year: sixth.getFullYear(),
                    month:sixth.getMonth() + 1,
                    date: sixth.getDate()
                }
            ]
        },
        format : function(n){
            return (n < 10 ? '0' : '') + n;
        },
        ajax : function(code, option){
            var that = this,
                city = option.city;

            //调用接口，天气信息
            that.currentOption = option;
            var JSONP=document.createElement("script");
            JSONP.type="text/javascript";
            JSONP.src="http://uniqlo.bigodata.com.cn/u1_5/index.php/Index/GetWeatherByCityID?callback=weatherJsonpCallback&id="+code;
            document.getElementsByTagName("head")[0].appendChild(JSONP);

        },
        setText : function(info, option){

            var time  = this.time();
            var index = option.index || 1;
            var num = option.second ? 0 : index * 2 - 1
            var arrIndex;
            var avg = 10;
            var weatherinfo = info['weather' + index];
            if(info['weather' + index].lt != null){
                avg = Math.ceil( (parseInt(weatherinfo.lt) + parseInt(weatherinfo.ht)) / 2);
            }

            $('#nio-img').attr({'title': weatherinfo.wt, 'class': 'nio-' + weatherinfo.di});

            $('#nio-kv').css('background-image', 'url('+option.imgpath+'/images/index/uniqlo-bg/'+ weatherinfo.di + '.jpg)');
            $('#nio-city').text(option.city).attr('title', option.city);
            $('#nio-date').text(time[index - 1].year + '年' + time[index - 1].month + '月' + time[index - 1].date + '日');
            $('#nio-day').text(time[index - 1].day).attr('title', time[index - 1].day);
            $('#nio-wea').text(weatherinfo.wt);
            $('#nio-low').html(weatherinfo.lt + '&deg;');
            $('#nio-high').html(weatherinfo.ht + '&deg;');
//气温降4度
            if(avg >14){
                if(avg >= 24) arrIndex = 0
                else if(avg >= 19) arrIndex = 1
                else arrIndex = 2
            } else {
                if(avg >= 11) arrIndex = 3
                else if(avg >= 6) arrIndex = 4
                else if(avg >= 1) arrIndex = 5
                else arrIndex = 6
            }

            $('#nio-tip').text(this.tips[arrIndex]).attr('title', this.tips[arrIndex]);

            $('#nio-day1').text(time[0].day);
            $('#nio-day2').text(time[1].day);
            $('#nio-day3').text(time[2].day);
            $('#nio-day4').text(time[3].day);
            $('#nio-day5').text(time[4].day);
            $('#nio-day6').text(time[5].day);

            $('#nio-tem1').html(info['weather1'].ht+ '&deg;');
            $('#nio-tem2').html(info['weather2'].ht+ '&deg;');
            $('#nio-tem3').html(info['weather3'].ht+ '&deg;');
            $('#nio-tem4').html(info['weather4'].ht+ '&deg;');
            $('#nio-tem5').html(info['weather5'].ht+ '&deg;');
            $('#nio-tem6').html(info['weather6'].ht+ '&deg;');

            this[option.city] = info['cityname'];
            var temper = {low: weatherinfo.lt,
                high: weatherinfo.ht};
            if(typeof option.callback == 'function'){
                option.callback(option.province, temper, info);
            }
        },
        temp : function(str){
            var first, second, low = 0, high = 0;
            if(str){
                str = str.split('~');
                first = parseInt(str[0], 10);
                second = parseInt(str[1], 10);
                low = Math.min(first, second);
                high = Math.max(first, second);
            }
            return {
                l  : low + '&deg;',
                h : high + '&deg;',
                av : Math.ceil((low + high) / 2),
                low: low,
                high : high
            }
        },
        init : function(option){
            option = option || {}
            var city = option.city
            if(city === remote_ip_info['city'] && this[city]) return this.setText(this[city], option);
            city = remote_ip_info['city'] = city || remote_ip_info['city'];
            for (var i = 0, len = citys.length; i < len; i ++) {
                if(citys[i].n === city) break;
            }
            option.city = city
            if(citys[i]) this.ajax(citys[i].i, option);
            else {
                option.city = '上海';
                this.ajax(101020100, option);
            }
        }
    };

//weather.init();

    /*=================================*/
// $.weather.init({
// 		city : city,
// 		callback: function(city, temper, info){
// 			// city 城市名
// 			// temper: {
//			// 	low: 最低气温
//			// 	high: 最高气温
//			// }
// 			// info 整条info数据
// 		}
// })
    /*=================================*/

    $.weather = weather;

    /*== mini-city ==*/

    if(typeof data != 'undefined'){
        var sel = new LinkageSelect({data : data});
        sel.bind('.linkageselect .level_1');
        sel.bind('.linkageselect .level_2');
        sel.bind('.linkageselect .level_3');
    }
    var location = $('.mini-city');
    $('.mini-change-btn').on('click', function(){
        location.toggle();
    });

    location.submit(function(){
        var that = $(this),
            option = that.find('option:checked'),
            province = option.first().text(),
            prov = province,
            city = option.last().text(),
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
            $.pron = prov;
            weather.init({'city' : city, 'province': prov,imgpath : window.imgpath,
                callback: function(city, temper, info){
                    var avg = getavg(temper.high,temper.low);
                    $.weather.avg = avg;
                    avg = avg?avg:0;
                    $.weather.occasion = $.weather.occasion?$.weather.occasion:0;
                    $.weather.sex = $.weather.sex?$.weather.sex:0;
                    $.weather.set = $.weather.set?$.weather.set:0;
                    var JSONP=document.createElement("script");
                    JSONP.type="text/javascript";
                    JSONP.src="http://uniqlo.bigodata.com.cn/u1_5/index.php/Index/getgood?callback=jsonpCallback4&tem="+avg+"&pro="+city+'&cid='+$.weather.occasion+'&sid='+$.weather.sex+'&tid='+$.weather.set;
                    document.getElementsByTagName("head")[0].appendChild(JSONP);

                    /*$.post($.weather.getgurl,{
                     pro : city,
                     tem : avg,
                     cid : $.weather.occasion,
                     sid : $.weather.sex,
                     tid : $.weather.set
                     },function(data,status){
                     var da = eval("("+data+")");
                     if(da){
                     if(da.flag1=='p'){
                     if($.weather.set==1){
                     if(da.ustr){
                     $('#upc').html(da.ustr);
                     }
                     if(da.dstr){
                     $('#downc').html(da.dstr);
                     }
                     }else{
                     $('#upc').html(da.ustr);
                     $('#downc').html(da.dstr);
                     }
                     }
                     if(da.flag=='t'){
                     $('#taoz').html(da.tstr);
                     $.uniqlo.kvSlider();
                     }
                     if($.weather.set==1){
                     if(!da.ustr && !da.dstr && !da.tstr){
                     $('#tishi').css('display','block');
                     }else if(da.tstr){
                     $('#tishi').css('display','none');
                     }
                     }else{
                     if(!da.ustr && !da.dstr){
                     $('#tishi').css('display','block');
                     }else{
                     $('#tishi').css('display','none');
                     }
                     }

                     }
                     });*/
                    //往mimi传城市
                    sendcity(city,info.city);
                }
            });
            that.hide();
            $.uniqlo.index.togClass($.uniqlo.index.week.find('li').first(), 'mini-checked')

        } else alert('请选择城市！');
        return false;

    }).on('click', 'a.mini-city-close', function(){
        location.hide();
    });

});