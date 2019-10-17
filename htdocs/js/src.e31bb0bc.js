// tooltip https://github.com/chrisdavies/tlite
function tlite(t){document.addEventListener("mouseover",function(e){var i=e.target,n=t(i);n||(n=(i=i.parentElement)&&t(i)),n&&tlite.show(i,n,!0)})}tlite.show=function(t,e,i){var n="data-tlite";e=e||{},(t.tooltip||function(t,e){function o(){tlite.hide(t,!0)}function l(){r||(r=function(t,e,i){function n(){o.className="tlite tlite-"+r+s;var e=t.offsetTop,i=t.offsetLeft;o.offsetParent===t&&(e=i=0);var n=t.offsetWidth,l=t.offsetHeight,d=o.offsetHeight,f=o.offsetWidth,a=i+n/2;o.style.top=("s"===r?e-d-10:"n"===r?e+l+10:e+l/2-d/2)+"px",o.style.left=("w"===s?i:"e"===s?i+n-f:"w"===r?i+n+10:"e"===r?i-f-10:a-f/2)+"px"}var o=document.createElement("span"),l=i.grav||t.getAttribute("data-tlite")||"n";o.innerHTML=e,t.appendChild(o);var r=l[0]||"",s=l[1]||"";n();var d=o.getBoundingClientRect();return"s"===r&&d.top<0?(r="n",n()):"n"===r&&d.bottom>window.innerHeight?(r="s",n()):"e"===r&&d.left<0?(r="w",n()):"w"===r&&d.right>window.innerWidth&&(r="e",n()),o.className+=" tlite-visible",o}(t,d,e))}var r,s,d;return t.addEventListener("mousedown",o),t.addEventListener("mouseleave",o),t.tooltip={show:function(){d=t.title||t.getAttribute(n)||d,t.title="",t.setAttribute(n,""),d&&!s&&(s=setTimeout(l,i?150:1))},hide:function(t){if(i===t){s=clearTimeout(s);var e=r&&r.parentNode;e&&e.removeChild(r),r=void 0}}}}(t,e)).show()},tlite.hide=function(t,e){t.tooltip&&t.tooltip.hide(e)},"undefined"!=typeof module&&module.exports&&(module.exports=tlite);

window.onload = function() {
    // init tooltip with class js--tooltip
    tlite(el => {
        return {
            el: el.classList.contains('js--tooltip'), grav: 'sw',
        }
    });

    var acc = document.getElementsByClassName("ww-collapse__head");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("is--active");
        });
    }

    // config for graphics
    const circleChartNode = document.getElementById('circleChart');
    const lineChartNode = document.getElementById('lineChart');
    if (circleChartNode) {
        const ctx = circleChartNode.getContext('2d');
        const colors = {
            'error': '#EE3F3F',
            'normal': '#3D50DF',
            'success': '#4BC374',
        };

        function getColor(number) {
            switch (true) {
                case number <= 20:
                    return colors.error;
                case number <= 80:
                    return colors.normal;
                case number <= 100:
                    return colors.success;
            }
        }

        let availability = 0;
        let max = 100;
        if(typeof availability_percent != "undefined"){
            availability = parseInt(availability_percent);
        }
        let color = getColor(availability);
        document.querySelector(".ww-graph__percent").style.setProperty("color",color,'important');
        document.querySelector(".ww-graph__title").style.setProperty("color",color,'important');
        const config = {
            type: 'doughnut',
            data: {
                labels: ['', ''],
                datasets: [{
                    label: '',
                    data: [availability, max-availability],
                    backgroundColor: [
                        color,
                        '#DFE0E9',
                    ],
                    borderWidth: 0,
                    weight: 1,
                }]
            },
            options: {
                tooltips: {
                    enabled: false,
                },
                legend: {
                    display: false,
                },
                rotation: 1 * Math.PI,
                circumference: 1 * Math.PI,
                cutoutPercentage: 95,
            }
        };
        new Chart(ctx, config);
    }

    if (lineChartNode) {
        function last_7_days () {
            return '0123456'.split('').map(function(n) {
                var d = new Date();
                d.setDate(d.getDate() - n);

                return (function(day, month, year) {
                    return [day<10 ? '0'+day : day, month<10 ? '0'+month : month].join('/');
                })(d.getDate(), d.getMonth(), d.getFullYear());
            }).join(',');
        }
        const lineChart = document.getElementById('lineChart').getContext('2d');
        let _data = [];
        let _days = [];
        for(let data of Object.keys(waf_chart)){
            _data.push(waf_chart[data].count);
        }
        for(let days of Object.keys(waf_chart)){
            _days.push(waf_chart[days].day);
        }
        const lineConfig = {
            type: 'line',
            data: {
                labels: _days,
                datasets: [{
                    borderColor: 'rgba(61, 80, 223)',
                    data: _data,
                    type: 'line',
                    pointRadius: 0,
                    fill: false,
                    lineTension: 0,
                    borderWidth: 2
                }]
            },
            options: {
                tooltips: {
                    enabled: false,
                },
                legend: {
                    display: false,
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            source: 'data',
                            autoSkip: true,
                            fontWeight: 500,
                            fontSize: 10,
                            fontColor: '#ACAFBF',
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: true,
                            borderDash: [2],
                            tickMarkLength: 3,
                            color: ['', 'rgba(15,15,15, .4)', 'rgba(15,15,15, .4)', 'rgba(15,15,15, .4)'],
                            drawBorder: false,
                        },
                        ticks: {
                            stepSize: 25,
                            fontColor: 'rgba(61, 80, 223)',
                            fontSize: 10,
                            fontFamily: 'Roboto',
                        },
                        position: 'right',
                        scaleLabel: {
                            display: true,
                        }
                    }]
                },
            }
        };
        new Chart(lineChart, lineConfig);
    }
};

if (window.Element && !Element.prototype.closest) {
    Element.prototype.closest =
        function(s) {
            var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                i,
                el = this;
            do {
                i = matches.length;
                while (--i >= 0 && matches.item(i) !== el) {};
            } while ((i < 0) && (el = el.parentElement));
            return el;
        };
}

var btn = document.getElementsByClassName('v-pause');
var checkbox = document.getElementsByClassName('v-change');
for(b of btn){
    b.addEventListener("click", function (e) {
        var $this = this;
        var selector = this.closest(".ww-main");
        let params = '&action=change_status&host_id=' + this.getAttribute('data-host_id') + '&config_id=' + this.getAttribute('data-config_id');
        function callback(http) {
            let data = JSON.parse(http.responseText);
            if (data.data.toggleServiceConfig !== null && data.data.toggleServiceConfig.isActive) {
                $this.classList.remove("ww-icon--play");
                $this.classList.add("ww-icon--pause");
            } else {
                $this.classList.add("ww-icon--play");
                $this.classList.remove("ww-icon--pause");
            }
            if(selector) {
                selector.classList.remove("is--loading");
            }
        }

        if(selector){
            selector.classList.add("is--loading");
        }

        (new WTRequest(params, ajaxurl, 'POST', callback)).send();
    });
}

for(check of checkbox){
    check.addEventListener("change", function (e) {
        var $this = this;
        var selector = this.closest(".ww-option");
        let params = '&action=change_status&host_id=' + this.getAttribute('data-host_id') + '&config_id=' + this.getAttribute('data-config_id');
        function callback(http) {
            let data = JSON.parse(http.responseText);
            if (data.data.toggleServiceConfig !== null && data.data.toggleServiceConfig.isActive) {
                $this.checked = true;
            } else {
                $this.checked = false;
            }
            if(selector) {
                selector.classList.remove("is--loading");
            }
        }
        if(selector){
            selector.classList.add("is--loading");
        }

        (new WTRequest(params, ajaxurl, 'POST', callback)).send();
    });
}

window.WTRequest = function (params,url = '',method = '',callback = '') {
    if(url === ''){
        url = ajaxurl;
    }
    if(method === ''){
        method = 'POST';
    }
    this.url = url;
    this.method = method;
    this.params = params;
    var defaultFunction = function (http) {
        if(http.status === 200){
            location.reload();
        }
    }
    this.send = function () {
        var http = new XMLHttpRequest();
        http.open(this.method, this.url, true);
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.onreadystatechange = function () {
            if(http.readyState === 4 && http.status === 200) {
                if(callback === ''){
                    defaultFunction(http);
                }else{
                    callback(http);
                }
            }
        }
        http.send(this.params);
    }
}

function removeAlert() {
    var alertNode = document.getElementById('ww-alert');

    if (alertNode) {
        alertNode.parentNode.removeChild(alertNode);
    }
}