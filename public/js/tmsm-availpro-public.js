(function( $ ) {
	'use strict';

  var tmsm_availpro_calendar = $('#tmsm-availpro-calendar');

  if(tmsm_availpro_calendar.length > 0){

    var tmsm_availpro_calendar_today = moment().subtract(1, 'days');
    var tmsm_availpro_calendar_startdate = moment();
    var tmsm_availpro_calendar_enddate = moment().add(1, 'year');

    var tmsm_availpro_calendar_selected_date;
    var tmsm_availpro_calendar_selected_begin;
    var tmsm_availpro_calendar_selected_end;
    var tmsm_availpro_calendar_lastdateclicked;
    var tmsm_availpro_calendar_current_year;
    var tmsm_availpro_calendar_current_month;
    var tmsm_availpro_calendar_nights = 1;
    var tmsm_availpro_calendar_minstay = 0;

    /**
     * Set Calendar Events for Month
     *
     * @param month
     */
    var tmsm_availpro_calendar_set_events = function(month){

      tmsm_availpro_calendar_current_year = month.format('YYYY');
      tmsm_availpro_calendar_current_month = month.format('MM');

      var events_toload = [];
      if (typeof tmsm_availpro_params.data !== 'undefined') {

        if (typeof tmsm_availpro_params.data[month.format('YYYY-MM')] !== 'undefined') {
          var days = tmsm_availpro_params.data[month.format('YYYY-MM')];
          var events = [];
          var i = 0;
          var lowest_price = null;

          // Get lowest price
          $.each(days, function (index, value) {
            if (typeof value.Price !== 'undefined' && value.Status !=='NotAvailable' ) {
              if(lowest_price === null){
                lowest_price = Number(value.Price);
              }
              if(Number(value.Price) < lowest_price){
                lowest_price = Number(value.Price);
              }
            }
          });

          // Create Events
          $.each(days, function (index, value) {
            value.date = index;
            if (typeof value.Price !== 'undefined' && value.Status !=='NotAvailable') {
              value.PriceWithCurrency = Number(value.Price).toLocaleString(tmsm_availpro_params.locale,
                {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});

              value.Test = tmsm_availpro_params.locale;
              if(Number(value.Price) === lowest_price){
                value.LowestPrice=1;
              }
              events[i] = value;
              events_toload.push(events[i]);
              i++;
            }

          });
          tmsm_availpro_calendar_clndr.addEvents(events_toload);
        }

      }


    }

    var setCalendarWidth = function(){

      if(tmsm_availpro_calendar.width()> 600){
        tmsm_availpro_calendar.addClass('calendar-large');
        tmsm_availpro_calendar.removeClass('calendar-small');
      }
      else{
        tmsm_availpro_calendar.addClass('calendar-small');
        tmsm_availpro_calendar.removeClass('calendar-large');
      }
    };
    $( window ).resize(function() {
      setCalendarWidth();
    });
    setCalendarWidth();

    // Clndr
    var tmsm_availpro_calendar_clndr = tmsm_availpro_calendar.clndr({
      template: $('#tmsm-availpro-calendar-template').html(),
      startWithMonth: tmsm_availpro_calendar_startdate,
      constraints: {
        startDate: tmsm_availpro_calendar_today,
        endDate: tmsm_availpro_calendar_enddate
      },
      adjacentDaysChangeMonth: true,
      forceSixRows: false,
      trackSelectedDate: false,
      clickEvents: {
        click: function(target) {

          var reoderdates = false;

          $('.day.mouseover').removeClass('mouseover');

          //if(target.events.length && !$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
          if(!$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
            //$('.day').removeClass('selected').removeClass('selected-range').removeClass('active');

            tmsm_availpro_calendar_lastdateclicked = target.date;


            // Reorder dates
            if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined'){
              if(tmsm_availpro_calendar_selected_begin > tmsm_availpro_calendar_lastdateclicked){

                $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD')).removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');

                reoderdates = true;
                tmsm_availpro_calendar_selected_begin = undefined;
                tmsm_availpro_calendar_selected_end = undefined;
              }
              else{
                reoderdates = false;
              }
            }
            else{
              reoderdates = false;
            }

            // Reinitialize selected days if begin and end have both been initialized
            if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined'){
              $('.day').removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
              tmsm_availpro_calendar_selected_begin = undefined;
              tmsm_availpro_calendar_selected_end = undefined;
              $('#tmsm-availpro-form-checkoutdateinfo').html('');
              $('#tmsm-availpro-form-checkoutdate').val('');
            }

            // Begin date not initialized
            if(typeof tmsm_availpro_calendar_selected_begin === 'undefined'){
              tmsm_availpro_calendar_selected_begin = tmsm_availpro_calendar_lastdateclicked;
              $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD')).addClass('selected selected-begin');
              $('#tmsm-availpro-form-checkindateinfo').html(tmsm_availpro_calendar_selected_begin.format('L'));
              $('#tmsm-availpro-form-checkindate').val(tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD'));
              $('#tmsm-availpro-form-arrivaldate').val(tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD'));

              tmsm_availpro_calendar_minstay = $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD') +' .cell').data('minstay');
              if(!tmsm_availpro_calendar_minstay){
                tmsm_availpro_calendar_minstay = 0;
              }
              $('#tmsm-availpro-form-minstay-message').attr('data-value', tmsm_availpro_calendar_minstay);
              $('#tmsm-availpro-form-minstay-number').html(tmsm_availpro_calendar_minstay);

            }
            else{
              if(reoderdates === false){
                tmsm_availpro_calendar_selected_end = tmsm_availpro_calendar_lastdateclicked;

                // Check if dates respect minstay
                if (tmsm_availpro_calendar_minstay > 0) {
                  var checkminstay = moment(tmsm_availpro_calendar_selected_begin);
                  if (checkminstay.add(tmsm_availpro_calendar_minstay, 'days') > tmsm_availpro_calendar_selected_end) {
                    console.warn('doest not respect minstay');
                    tmsm_availpro_calendar_selected_end = undefined;
                  }
                  else {

                  }
                }
                if( typeof tmsm_availpro_calendar_selected_end !== 'undefined'){
                  $('.calendar-day-' + tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD')).addClass('selected selected-end');
                }
              }
            }

            // Calculate nights
            $('#tmsm-availpro-form-dates-container').hide();
            if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined'){
              tmsm_availpro_calendar_nights = tmsm_availpro_calendar_selected_end.diff(tmsm_availpro_calendar_selected_begin, "days");
              $('#tmsm-availpro-form-checkoutdateinfo').html(tmsm_availpro_calendar_selected_end.format('L'));
              $('#tmsm-availpro-form-checkoutdate').val(tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD'));

              $('#tmsm-availpro-form-dates-container').show();

              // Submit calculate total price
              $('#tmsm-availpro-calculatetotal').submit();
            }
            else{
              tmsm_availpro_calendar_nights = 0;
            }

            $('#tmsm-availpro-form-nights-number').html(tmsm_availpro_calendar_nights);
            $('#tmsm-availpro-form-nights-message').attr('data-value', tmsm_availpro_calendar_nights);
            $('#tmsm-availpro-form-nights').val(tmsm_availpro_calendar_nights);
            if(tmsm_availpro_calendar_nights > 0){
              $('#tmsm-availpro-form-minstay-message').attr('data-value', 0);
              $('#tmsm-availpro-form-minstay-number').html('');
            }
            else{
              $('#tmsm-availpro-calculatetotal-totalprice').hide();
              $('#tmsm-availpro-calculatetotal-ota').hide();
            }

          }
        },

        onMonthChange: tmsm_availpro_calendar_set_events

      },
      doneRendering: function() {
        var self = this;
        $(this.element).on('mouseover', '.day:not(.inactive)', function(e) {

          var target = self.buildTargetObject(e.currentTarget, true);
          var hover_begin = tmsm_availpro_calendar_selected_begin;
          var hover_end = target.date;


          // Over Select
          // Begin date already initialized
          if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end === 'undefined'){

            $('.day').removeClass('mouseover').removeClass('selected').removeClass('selected-hover').removeClass('selected-end');

            $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('selected selected-end');

            // selection
            var i = 0;
            var selectedDate = hover_begin;
            if(hover_end > selectedDate){
              $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');
              while(hover_end.format('YYYY-MM-DD') != selectedDate.format('YYYY-MM-DD')) {
                i++;
                selectedDate = moment(hover_begin).add(i, 'days');
                $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');

              }

            }

          }
          // Begin date not initialized
          else{
            $('.day.mouseover').removeClass('mouseover');
            $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('mouseover');
          }

        });
      }

    });

    tmsm_availpro_calendar_set_events(tmsm_availpro_calendar_startdate);

    // Calculate total price form
    $('#tmsm-availpro-calculatetotal').on('submit', function(e){
      e.preventDefault();

      // Reset value
      $('#tmsm-availpro-calculatetotal-totalprice-value').html('');
      $('#tmsm-availpro-calculatetotal-ota').html('');
      $('#tmsm-availpro-form-submit').prop('disabled', true);
      $('#tmsm-availpro-calculatetotal-loading').show();
      $('#tmsm-availpro-calculatetotal-errors').hide();
      $('#tmsm-availpro-form').removeClass('tmsm-availpro-form-has-ota-price');

      // Calculate date if begin and end are defined
      if(tmsm_availpro_calendar_selected_begin && tmsm_availpro_calendar_nights > 0){

        // Ajax call
        $.ajax({
          url: _wpUtilSettings.ajax.url,
          type: 'post',
          dataType: 'json',
          enctype: 'multipart/form-data',
          data: {
            action: 'tmsm-availpro-calculatetotal',
            date_begin: tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD'),
            date_end: tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD'),
            nights: tmsm_availpro_calendar_nights,
            security: $('#tmsm-availpro-calculatetotal-nonce').val(),
          },
          success: function (data) {
            $('#tmsm-availpro-calculatetotal-loading').hide();

            if (data.success === true) {
              $('#tmsm-availpro-form-submit').prop('disabled', false);
              $('#tmsm-availpro-calculatetotal-errors').hide();

              if(data.data.accommodation && data.data.accommodation.totalprice){
                var Price = data.data.accommodation.totalprice;
                if(Price){
                  $('#tmsm-availpro-calculatetotal-totalprice').show();
                  var PriceWithCurrency = Number(Price).toLocaleString(tmsm_availpro_params.locale, {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});
                  if(PriceWithCurrency){
                    $('#tmsm-availpro-calculatetotal-totalprice').html(_.unescape(tmsm_availpro_params.i18n.selecteddatepricelabel.replace(/%/g,PriceWithCurrency)));
                  }
                }
              }

              if(data.data.ota && data.data.ota.totalprice){
                var Price = data.data.ota.totalprice;
                if(Price){
                  $('#tmsm-availpro-form').addClass('tmsm-availpro-form-has-ota-price');

                  $('#tmsm-availpro-calculatetotal-ota').show();
                  var PriceWithCurrency = Number(Price).toLocaleString(tmsm_availpro_params.locale, {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});
                  if(PriceWithCurrency){
                    $('#tmsm-availpro-calculatetotal-ota').html(_.unescape(tmsm_availpro_params.i18n.otacomparelabel.replace(/%/g,PriceWithCurrency)));
                  }
                }
                else{
                  $('#tmsm-availpro-form').removeClass('tmsm-availpro-form-has-ota-price');
                }
              }

            }
            else {
              $('#tmsm-availpro-form-submit').prop('disabled', true);
              $('#tmsm-availpro-calculatetotal-totalprice').hide();
              $('#tmsm-availpro-calculatetotal-ota').hide();
              $('#tmsm-availpro-calculatetotal-errors').show().html(data.errors);
              $('#tmsm-availpro-form').removeClass('tmsm-availpro-form-has-ota-price');
            }
          },
          error: function (jqXHR, textStatus) {
            console.log('error');
            console.log(jqXHR);
            console.log(textStatus);
          }
        });
      }

    });


    $('#tmsm-availpro-form-submit').on('click', function(e){
      e.preventDefault();
      $(this).closest('form').submit();
    });

    // Display year best price in shortcode
    var tmsm_availpro_bestprice_year = $('.tmsm-availpro-bestprice-year');
    if(tmsm_availpro_bestprice_year.length > 0){
      tmsm_availpro_bestprice_year.each(function(e){
        if($(this).data('price')){
          $(this).html(_.unescape(tmsm_availpro_params.i18n.yearbestpricelabel.replace('%',Number($(this).data('price')).toLocaleString(tmsm_availpro_params.locale, {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0}))));
        }
      });

    }
  }

})( jQuery );
