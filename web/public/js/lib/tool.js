/**
 * 自定义工具库
 * 
 */
(function(root, factory) {
    var tool = factory(root);
    if (typeof define === 'function' && define.amd) {
        // AMD
        // define([], factory);
        define('tool', function() { return tool; });
    } else if (typeof exports === 'object') {
        // Node.js
        module.exports = tool;
    } else {
        // Browser globals
        root.tool = tool;
    }
})(this, function() {
    var tool;
    tool = {
        /**
         * 是否微信浏览器
         * 
         */
        isWeixin: function() {
            return /micromessenger/.test(navigator.userAgent.toLowerCase());
        },


        /**
         * 查找url后面参数
         * 
         */
        getParam: function(key) {
            var url = window.location.href,
                index = url.indexOf("?"),
                keyArr;
            if (index >= 0) {
                keyArr = url.substr(index + 1).split("#")[0].split("&");
                for (var i = 0; i < keyArr.length; i++) {
                    if (keyArr[i].split("=")[0] == key)
                        return keyArr[i].split("=")[1] || "";
                }
            }
            return undefined;
        },

        /**
         * 设置url参数，并刷新页面
         * 当需要删除某个key时，将值设置为undefined
         * 
         */
        setParam: function(key, val) {
            var href = window.location.href,
                index = href.indexOf("?"),
                hashIndex = href.indexOf("#"),
                url = "",
                urlParam = "",
                hashParam = "",
                paramArr = [],
                paramSet = {},
                oldParamArr = [],
                newParamArr = [],
                newParamStr = "";

            //url中存在？
            if (index > 0) {
                url = href.substr(0, index);
                if (hashIndex > 0) {
                    urlParam = href.substr(index + 1, hashIndex - index - 1);
                } else {
                    urlParam = href.substr(index + 1);
                }
            } else if (hashIndex > 0) {
                url = href.substr(0, hashIndex);
            } else {
                url = href;
            }

            if (hashIndex > 0) {
                hashParam = href.substr(hashIndex);
            }
            //查找参数
            paramArr = urlParam.split("&");

            //判断参数类型
            if (typeof key == "string") {
                paramSet[key] = val;
            } else if (typeof key == "object" && !(key instanceof Array)) {
                paramSet = key;
            } else {
                //不支持的类型
                return;
            }

            for (var i = 0; i < paramArr.length; i++) {
                var tmpKV = paramArr[i].split("=");
                if (tmpKV.length != 2)
                    continue;

                oldParamArr.push([tmpKV[0], tmpKV[1]]);
            }

            //拼凑urlParm
            for (var i = 0; i < oldParamArr.length; i++) {
                if (oldParamArr[i][0] in paramSet) {
                    if (paramSet[oldParamArr[i][0]] != undefined) {
                        newParamArr.push(oldParamArr[i][0] + "=" + paramSet[oldParamArr[i][0]]);
                    }
                    delete paramSet[oldParamArr[i][0]];
                } else {
                    newParamArr.push(oldParamArr[i][0] + "=" + oldParamArr[i][1]);
                }
            }
            for (var key in paramSet) {
                if (paramSet[key] == undefined)
                    continue;
                newParamArr.push(key + "=" + paramSet[key]);
            }

            newParamStr = newParamArr.join("&");
            if (newParamStr != "") {
                newParamStr = "?" + newParamStr;
            }

            console.log(url + newParamStr + hashParam);

            window.location.href = url + newParamStr + hashParam;
        },

        /**
         * 查找#后面参数
         * 
         */
        getLocalParam: function(key) {
            var url = window.location.href,
                index = url.indexOf("#"),
                keyArr;
            if (index >= 0) {
                keyArr = url.substr(index + 1).split("&");
                for (var i = 0; i < keyArr.length; i++) {
                    if (keyArr[i].split("=")[0] == key)
                        return keyArr[i].split("=")[1] || "";
                }
            }
            return undefined;
        },

        /***
         * 增添或修改#后面参数
         * 需自行编码
         * 
         */
        setLocalParam: function(key, val) {
            if (typeof key == 'object' && key instanceof Object) {
                for (var name in key) {
                    arguments.callee(name, key[name]);
                }
                return;
            }
            var url = window.location.href,
                index = url.indexOf("#"),
                key = key.toLowerCase(),
                keyArr,
                reg = new RegExp('(' + key + '=[^&]*)');
            if (index >= 0) {
                keyArr = url.substr(index + 1);
                if (keyArr.match(reg)) {
                    //已存在
                    window.location.href = url.replace(reg, key + '=' + val);
                } else {
                    if (keyArr == "") {
                        window.location.href = url + key + '=' + val;
                    } else {
                        window.location.href = url + "&" + key + '=' + val;
                    }
                }
            } else {
                window.location.href = url + '#' + key + '=' + val;
            }
        },

        /**
         * 获取根目录
         * 
         * 如 http://localhost/index.html， 返回http://localhost
         * 
         */
        getRootPath: function() {
            var rootPath = window.location.href.match(/^https?:\/\/[^\/]+/);
            if (rootPath == null) {
                return rootPath;
            }
            return rootPath[0];
        },
        /**
         * 获取url
         * 
         * 如 http://localhost/index.html， 返回http://localhost
         * 
         */
        getPathName: function() {
            var pathname = window.document.location.pathname;
            return pathname;
        },

        /**
         * 格式化输出时间（参照PHP date函数）
         * @param format
         * 		  (默认值为"Y-m-d H:i:s")
         * 		  Y - 4 位数字完整表示的年份 例如：1999 或 2003
         * 		  y - 2 位数字表示的年份 例如：99 或 03 
         * 		  m - 数字表示的月份，有前导零 01 到 12 
         * 		  n - 数字表示的月份，没有前导零 1 到 12 
         * 		  d - 月份中的第几天，有前导零的 2 位数字 01 到 31 
         * 		  j - 月份中的第几天，没有前导零 1 到 31 
         * 		  w - 星期中的第几天，数字表示 0（表示星期天）到 6（表示星期六） 
         * 		  l - （“L”的小写字母） 星期几，完整的文本格式 星期一 到 星期天 
         * 		  g - 小时，12 小时格式，没有前导零 1 到 12 
         *		  G - 小时，24 小时格式，没有前导零 0 到 23 
         * 		  h - 小时，12 小时格式，有前导零 01 到 12 
         * 		  H - 小时，24 小时格式，有前导零 00 到 23 
         * 		  i - 有前导零的分钟数 00 到 59
         * 		  s - 秒数，有前导零 00 到 59
         * 		 （其他格式待补充）
         * @param microsecond
         * 		  微秒数，如果没有给出则使用本地当前时间
         */
        dateFormat: function(format, microsecond) {
            var options = {
                    format: "Y-m-d H:i:s",
                    microsecond: new Date().getTime()
                },
                date, mapTable, outPutArr = [];
            //映射表
            mapTable = {
                'Y': function(date) { return date.getFullYear(); },
                'y': function(date) { return (date.getFullYear() + "").substr(2); },
                'm': function(date) { return date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1; },
                'n': function(date) { return date.getMonth() + 1; },
                'd': function(date) { return date.getDate() < 10 ? "0" + date.getDate() : date.getDate(); },
                'j': function(date) { return date.getDate(); },
                'w': function(date) { return date.getDay(); },
                'l': function(date) { return ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'][date.getDay()]; },
                'g': function(date) { return date.getHours() > 12 ? date.getHours() - 12 : date.getHours(); },
                'G': function(date) { return date.getHours(); },
                'h': function(date) { var hour; return (hour = date.getHours() > 12 ? date.getHours() - 12 : date.getHours()) < 10 ? "0" + hour : hour; },
                'H': function(date) { return date.getHours() < 10 ? "0" + date.getHours() : date.getHours(); },
                'i': function(date) { return date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(); },
                's': function(date) { return date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds(); }
            };
            if (arguments.length == 1) {
                if (/^\d+$/.test(arguments[0])) options.microsecond = arguments[0];
                else if (typeof arguments[0] === "string") {
                    options.format = arguments[0];
                }
            }
            if (arguments.length == 2) {
                options.format = format;
                options.microsecond = microsecond;
            }
            //适配秒
            if (/^\d{10}$/.test(options.microsecond)) {
                options.microsecond = options.microsecond * 1000;
            }
            date = new Date();
            date.setTime(parseInt(options.microsecond));
            //开始替换
            outPutArr.push(options.format);
            outPutArr.key = "";
            for (var key in mapTable) {
                doSplit(outPutArr, key);
            }
            return doJoin(outPutArr);

            //循环构建数组
            function doSplit(array, key) {
                for (var i = 0; i < array.length; i++) {
                    if (array[i] instanceof Array) arguments.callee(array[i], key);
                    else {
                        var newArr = [];
                        newArr = array[i].split(key);
                        //存在匹配项
                        if (newArr.length > 1) {
                            newArr.key = key;
                            array[i] = newArr;
                        }
                    }
                }
            }

            //遍历数组，输出格式化后的时间字符串
            function doJoin(array) {
                for (var i = 0; i < array.length; i++) {
                    if (array[i] instanceof Array) {
                        array[i] = arguments.callee(array[i]);
                    }
                }
                return array.join(mapTable[array.key] && mapTable[array.key](date) || "");
            }
        },

        /**
         * 格式化数字
         * 无法处理时直接返回原值
         * 
         * @param number
         * @param options 设置
         * 		   - digit	保留小数点位数，默认为2
         * 		   - round	是否四舍五入，默认true
         * 		   - trim	是否去除多余的0，默认true，当为false时，小数保留digit位，不足时补0
         * 		   - split	每三位的隔开符，默认空
         * 
         * @return number
         */
        numberFormat: function(number, options) {
            var defaultOptions = {
                    digit: 2,
                    round: true,
                    trim: true,
                    split: ""
                },
                tmp, len, arr, index, head, headArr, stern, sternArr, splitCount = 0;
            options = this.extend(defaultOptions, options || {});

            //强制转换至字符串
            number = number + "";

            //空字符串作为零值
            if (number === "") {
                number = "0";
            }

            //尚不兼容科学计数法
            if (!/^\d*(?:\.\d*)?$/.test(number))
                return number;

            //处理(.1)(1.)(.)特殊情况
            if (/^\./.test(number)) {
                number = "0" + number;
            }
            if (/\.$/.test(number)) {
                number = number.replace(".", "");
            }

            //先处理小数部分
            index = number.indexOf(".");
            //不存在小数
            if (index < 0) {
                if (options.digit > 0 && options.trim == false) {
                    number += ".";
                    for (var i = 0; i < options.digit; i++) {
                        number += "0";
                    }
                }
            } else {
                //存在小数
                arr = number.split(".");
                headArr = arr[0].split("");
                sternArr = arr[1].split("");

                for (var i = 0; i < options.digit; i++) {
                    tmp = sternArr.shift();
                    if (tmp == undefined)
                        headArr.push("0");
                    else
                        headArr.push(tmp);
                }
                
                head = parseInt(headArr.join(""));

                //判断是否需要进位
                if (options.round && sternArr.length > 0 && sternArr[0] >= 5) {
                    head = head + 1;
                }
                headArr = (head + "").split("");
                sternArr = headArr.splice(-(options.digit), options.digit);

                //修剪多余的0
                if (options.trim) {
                    for (var i = sternArr.length - 1; i >= 0; i--) {
                        if (sternArr[i] != "0")
                            break;

                        sternArr.pop();
                    }
                }

                number = (headArr.length == 0 ? "0" : headArr.join("")) + (sternArr.length == 0 ? "" : "." + sternArr.join(""));
            }

            //格式化金钱
            if (options.split != "") {
                tmp = "";
                arr = number.split(".");
                for (var i = arr[0].length - 1; i >= 0; i--) {
                    tmp = arr[0][i] + tmp;
                    splitCount++;
                    if (splitCount % 3 == 0 && i != 0) {
                        tmp = options.split + tmp;
                        splitCount = 0;
                    }
                }
                arr[0] = tmp;
                number = arr.join(".");
            }

            return number;
        },

        /**
         * 合并
         * （tool没有依赖jquery，extend需自行实现）
         * 
         * @param  Objects
         * @return Object
         * 
         */
        extend: function() {
            var obj = {};
            for (var i = 0, len = arguments.length; i < len; i++) {
                if (!(arguments[i] instanceof Object))
                    continue;

                for (var key in arguments[i]) {
                    if (arguments[i][key] instanceof Object)
                        obj[key] = arguments.callee(obj[key] || {}, arguments[i][key]);
                    else
                        obj[key] = arguments[i][key];
                }
            }
            return obj;
        }
    };

    window.tool = tool;
    return tool;
});