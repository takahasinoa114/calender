let year, month;

console.log("calendar.js loaded");

$(function(){
    console.log("jQuery ready");
    drawCalendar( new Date() );
});

function drawCalendar( now ){
    console.log("drawCalendar called", now);
    year = now.getFullYear();
    month = now.getMonth();
    const firstDay = new Date(year, month, 1);
    const start = dateFormat( new Date(year, month, 1 - firstDay.getDay()) );
    const end = dateFormat( new Date(year, month, 1- firstDay.getDay() + 41) );
    const param1 = {
        url: 'holiday.php',
        type: 'get',
        data: { 'start': start, 'end': end },
        dataType: 'json'
    };
    $.ajax(param1)
      .done(function(holiday){
        const param2 = {
            url: 'schedule.php',
            type: 'get',
            data: { 'start': start, 'end': end },
            dataType: 'json'
        };
        $.ajax(param2)
          .done(function(schedule){
            draw(holiday, schedule);
          })
          .fail(function(jqXHR, textStatus, errorThrown){
            console.error('schedule.php error:', textStatus, errorThrown, jqXHR.responseText);
          });
      })
      .fail(function(jqXHR, textStatus, errorThrown){
        console.error('holiday.php error:', textStatus, errorThrown, jqXHR.responseText);
      });
}

function dateFormat( dt ){
    let year = dt.getFullYear();
    let month = dt.getMonth() + 1;
    let date = dt.getDate();
    return year + '-' + ('0' + month).slice(-2) + '-' + ('0' + date).slice(-2);
}

function draw( holiday, schedule ){
    let title = '<span id="prev-month"><</span>';
    title += year + '年' + (month + 1) + '月';
    title += '<span id="next-month">></span>';
    $('#title').html(title);

    
    const weekDays = ['日', '月', '火', '水', '木', '金', '土'];
    let html = '';
    for (let i = 0; i < 7; i++) {
        let className = '';
        if (i === 0) className = 'sun';
        if (i === 6) className = 'sat';
        html += '<div class="' + className + '"><b>' + weekDays[i] + '</b></div>';
    }

    let firstDay = new Date(year, month, 1);
    let startDate = new Date(year, month, 1 - firstDay.getDay());

    for (let i = 0; i < 42; i++) {
        let d = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + i);
        let y = d.getFullYear();
        let m = d.getMonth();
        let day = d.getDate();
        let strDate = dateFormat(d);

        let className = 'day';
        if (m !== month) {
            className += ' other-month';
        }
        if (d.getDay() === 0) {
            className += ' sun';
        }
        if (d.getDay() === 6) {
            className += ' sat';
        }
        if (holiday[strDate]) {
            className += ' holiday';
        }

        let s = '';
        if (holiday[strDate]) {
            s += '<div class="holiday-text">' + holiday[strDate] + '</div>';
        }
        if (schedule[strDate]) {
            s += '<div class="schedule-text">' + schedule[strDate] + '</div>';
        }

        html += '<div class="' + className + '" id="' + y + '-' + (m + 1) + '-' + day + '">';
        html += day;
        html += s;
        html += '</div>';
    }

    $('#calendar').html(html);

    $('.day').on('click', function () {
        windowOpen($(this).prop('id'));
    });

    $('#prev-month').on('click', function () {
        drawCalendar(new Date(year, month - 1, 1));
    });

    $('#next-month').on('click', function () {
        drawCalendar(new Date(year, month + 1, 1));
    });
}

function windowOpen( id ){
    let url = 'calendarEdit.php?id=' + id;
    const left =(screen.width - 600) / 2;
    const top = (screen.height - 320) / 2;
    window.open(url, null, 'width=600, height=320, top=' + top + ', left=' + left);
}

function windowClose(){
    const id = $('#id').val();
    let content = $('#content').val();
    content = content.replace(/\n/g, '<br>');

    const ajaxParam = {
        url: 'calendarUpdate.php', // 修正
        type: 'get',
        datatype: 'text',
        data: { id: id, content: content }
    }
    $.ajax(ajaxParam)
        .done( function( txt ){
            let ary = id.split('-');
            let year = parseInt(ary[0])
            let month = parseInt(ary[1]) - 1;
            window.opener.drawCalendar( new Date(year, month, 1) );
            self.close();
        });
}

window.drawCalendar = drawCalendar;