var Arnly = {};

if (!Arnly.hasOwnProperty('Wait'))
    Arnly.Wait = 0;

if (!Arnly.hasOwnProperty('Count'))
    Arnly.Count = 0;

if (!Arnly.hasOwnProperty('Edison'))
    Arnly.Edison = {};

/*
PARAMS:
MESSAGE
TYPE
*/
Arnly.showError = function (params = {})
{
    if (typeof params == undefined || !params)
        return alert('Arnly.showError: не переданы параметры.');

    if (typeof params.TYPE == undefined || !params.TYPE)
        params.TYPE = 'alert';

    if (typeof params.MESSAGE == undefined || !params.MESSAGE)
        return alert('Arnly.showError: params.MESSAGE - не переданы параметры.');

    if (params.TYPE == 'console')
        return console.dir(params.MESSAGE);

   return alert(params.MESSAGE);
}

/*
PARAMS:
STRING
*/
Arnly.isNumeric = function (params = {})
{
    return /^-?\d+$/.test(params.STRING);
}

/*
PARAMS:
STRING
*/
Arnly.Edison.CheckedInputData = function (params = {})
{
    if (typeof params.STRING == undefined || params.STRING == '')
        return Arnly.showError({MESSAGE: 'Не указаны данные.'});

    if (params.STRING.length < 2)
        return Arnly.showError({MESSAGE: 'Не достаточно количества символов.'});

    if (params.STRING.length > 2)
        return Arnly.showError({MESSAGE: 'Превышено количества символов.'});

    if (!Arnly.isNumeric({STRING: params.STRING}))
        return Arnly.showError({MESSAGE: 'Введенные данные не являются целым числом.'});

    return true;
}

/*
PARAMS:
SECONDS
SELECTOR
SIDE

EXAMPLE:
Arnly.Edison.Timer({SECONDS: 30, SELECTOR: '.arnly-side-timer>span', SIDE: 'ONE'})
*/
Arnly.Edison.Timer = function (params = {})
{
    if (typeof params.SECONDS == undefined || $(params.SECONDS) < 0)
        return Arnly.showError({MESSAGE: 'Arnly.Edison.Timer: params.SECONDS - Не переданы параметры.', TYPE: 'console'});

    if (typeof params.SELECTOR == undefined || $(params.SELECTOR) == '')
        return Arnly.showError({MESSAGE: 'Arnly.Edison.Timer: params.SELECTOR - Не переданы параметры', TYPE: 'console'});

    if (typeof params.SIDE == undefined || $(params.SIDE) == '')
        return Arnly.showError({MESSAGE: 'Arnly.Edison.Timer: params.SIDE - Не переданы параметры', TYPE: 'console'});

    $(params.SELECTOR).text(params.SECONDS);
    params.SECONDS -= 1;

    var timer = setInterval (function() {
        if (params.SECONDS <= 0) {
            clearInterval(timer);
            if (Arnly.Wait == 1) {
                if (params.SIDE == 'ONE')
                {
                    Arnly.Edison.OneSideAjax({TYPE: 'UPDATE'});
                    Arnly.Edison.Timer({SECONDS: 11, SELECTOR: '.arnly-side-timer>span', SIDE: 'ONE'});
                }
                else if (params.SIDE == 'TWO')
                {
                    Arnly.Edison.TwoSideAjax({TYPE: 'UPDATE'});
                    Arnly.Edison.Timer({SECONDS: 11, SELECTOR: '.arnly-side-timer>span', SIDE: 'TWO'});
                }
                /*Arnly.Count++;
                if (Arnly.Count == 1)
                {
                    $('.arnly-log-wrapper').html('Время истекло, обновите страницу.');
                    return false;
                }*/
            }
        }

        $(params.SELECTOR).text(params.SECONDS);
        params.SECONDS -= 1;
    }, 1000);
}

/*
PARAMS:
TYPE - ADD, UPDATE

EXAMPLE:
Arnly.Edison.OneSideAjax({TYPE: 'ADD'})
*/
Arnly.Edison.OneSideAjax = function (params = {})
{
    if (typeof params.TYPE == undefined || $(params.TYPE) == '')
        return Arnly.showError({MESSAGE: 'Arnly.Edison.OneSideAjax: params.TYPE - Не передан тип.'});

    $.ajax({
        method: 'POST',
        url: '/edison/lib/ajax.php',
        type: 'json',
        data: {NUMBER: $('input[name="arnly-oneside-value"]').val(), ID: $('input[name="ID"]').val(), TEMPLATE: 'ONESIDE', TYPE: params.TYPE}
    }).done(function(data) {
        if (data.result == 'success')
        {
            if (data.output.ID)
                $('input[name="ID"]').val(data.output.ID);

            if (data.output.WAIT) {
                $('.arnly-log-wrapper').html(data.output.WAIT);
                $('.arnly-side-timer').show();
                Arnly.Wait = 1;
            }
            else
            {
                $('.arnly-log-wrapper').html('');
                $('.arnly-side-timer').hide();
                $('.arnly-oneside-send').removeClass('disabled');
                Arnly.Wait = 0;
            }

            if (data.output.HISTORY)
                $('.arnly-oneside-history').html(data.output.HISTORY.join(', '));

            if (data.output.ANSWERS)
                $('.arnly-oneside-answers').html(data.output.ANSWERS.join(', '));

            if (data.output.AUTHORITY)
                $('.arnly-oneside-authority').html(data.output.AUTHORITY.join(', '));
        }
        else
        {
            Arnly.showError({MESSAGE: 'Arnly.Edison.OneSideAjax: Произошла ошибка:' + data.message ? data.message : 'Arnly.Edison.OneSideAjax - Ошибка сервера.'});
        }
    });
}

/*
PARAMS:
TYPE - ADD, UPDATE

EXAMPLE:
Arnly.Edison.TwoSideAjax({TYPE: 'ADD'})
*/
Arnly.Edison.TwoSideAjax = function (params = {})
{

    if (typeof params.TYPE == undefined || $(params.TYPE) == '')
        return Arnly.showError({MESSAGE: 'Arnly.Edison.TwoSideAjax: params.TYPE - Не передан тип.'});

    $.ajax({
        method: 'POST',
        url: '/edison/lib/ajax.php',
        type: 'json',
        data: {NUMBER: $('input[name="arnly-twoside-value"]').val(), ID: $('input[name="ID"]').val(), TEMPLATE: 'TWOSIDE', TYPE: params.TYPE}
    }).done(function(data) {
        if (data.result == 'success')
        {
            if (data.output.ID)
                $('input[name="ID"]').val(data.output.ID);

            if (data.output.WAIT) {
                $('.arnly-log-wrapper').html(data.output.WAIT);
                $('.arnly-side-timer').show();

                if (Arnly.Wait !== 1)
                {
                    Arnly.Edison.Timer({SECONDS: 10, SELECTOR: '.arnly-side-timer>span', SIDE: 'TWO'});
                    Arnly.Wait = 1;
                }
            }
            else
            {
                $('.arnly-log-wrapper').html('');
                $('.arnly-side-timer').hide();
                $('.arnly-twoside-send').removeClass('disabled');
                Arnly.Wait = 0;
            }

            if (data.output.ACCURACY)
                $('.arnly-log-wrapper').html(data.output.ACCURACY);

            if ($('.arnly-log-wrapper').html() !== 'Ждём число...')
            {
                 setTimeout(() => $('.arnly-log-wrapper').html(''), 500);
            }
        }
        else
        {
            Arnly.showError({MESSAGE: 'Arnly.Edison.TwoSideAjax: Произошла ошибка:' + data.message ? data.message : 'Arnly.Edison.TwoSideAjax - Ошибка сервера.'});
        }
    });
}

$(document).ready(function() {

    $('.arnly-main-left').on('click', function() {
        window.open(location.origin + location.pathname + '?T=ONESIDE', '_blank');
    });

    $('.arnly-main-left').hover(
        function() {
            $('.arnly-main-left').text('Я готов(а)! Примкнуть к светлой стороне...');
        }, function() {
            $('.arnly-main-left').text('Светлая сторона');
        }
    );

    $('.arnly-main-right').on('click', function() {
        window.open(location.origin + location.pathname + '?T=TWOSIDE', '_blank');
    });

    $('.arnly-main-right').hover(
        function() {
            $('.arnly-main-right').text('Я готов(а)! Примкнуть к тёмной стороне...');
        }, function() {
            $('.arnly-main-right').text('Темная сторона');
        }
    );

    $('.arnly-oneside-send').on('click', function() {
        if (Arnly.Edison.CheckedInputData({STRING : $('input[name="arnly-oneside-value"]').val()}))
        {
            $(this).addClass('disabled');
            Arnly.Edison.OneSideAjax({TYPE: 'ADD'});
            Arnly.Edison.Timer({SECONDS: 10, SELECTOR: '.arnly-side-timer>span', SIDE: 'ONE'});
        }
    });

    $('.arnly-twoside-send').on('click', function() {
        if (Arnly.Edison.CheckedInputData({STRING : $('input[name="arnly-twoside-value"]').val()}))
            Arnly.Edison.TwoSideAjax({TYPE: 'ADD'});
    });

    if ($('.arnly-twoside-send').length)
    {
        Arnly.Edison.TwoSideAjax({TYPE: 'UPDATE'});
    }
});