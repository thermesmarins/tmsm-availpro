(function( $ ) {
  'use strict';

  var wp = window.wp || {};

})( jQuery );


var TmsmAvailproApp = TmsmAvailproApp || {};

(function ($, TmsmAvailproApp) {
  'use strict';



  // Animate Display
  TmsmAvailproApp.animateTransition = function(element){
    element.show();
    $('html, body').animate({
      scrollTop: element.offset().top
    }, 400);
  };

  /**
   * Set initial data into view and start recurring display updates.
   */
  TmsmAvailproApp.init = function() {

    // Availpro calendar for shortcode [tmsm-availpro-calendar]
    var tmsm_availpro_calendar = $('#tmsm-availpro-calendar');

    if (tmsm_availpro_calendar.length > 0) {
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
      var tmsm_availpro_calendar_set_events = function (month) {
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
            // $.each(days, function (index, value) {
            //   if (typeof value.Price !== 'undefined' && value.Status !== 'NotAvailable') {
            //     if (lowest_price === null) {
            //       lowest_price = Number(value.Price);
            //     }
            //     if (Number(value.Price) < lowest_price) {
            //       lowest_price = Number(value.Price);
            //     }
            //   }
            // });
            $.each(days, function (index, value) {
              if (typeof value.totalPrice !== 'undefined' && value.Status !== 'NotAvailable') {
                if (lowest_price === null) {
                  lowest_price = Number(value.totalPrice);
                }
                if (Number(value.totalPrice) < lowest_price) {
                  lowest_price = Number(value.totalPrice);
                }
              }
            });

            // Create Events
            $.each(days, function (index, value) {
              value.date = index;
              // if (typeof value.Price !== 'undefined' && value.Status !== 'NotAvailable') {
              //   value.PriceWithCurrency = Number(value.Price).toLocaleString(tmsm_availpro_params.locale,
              //     {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 2});

              //   value.Test = tmsm_availpro_params.locale;
              //   if (Number(value.Price) === lowest_price) {
              //     value.LowestPrice = 1;
              //   }
              //   events[i] = value;
              //   events_toload.push(events[i]);
              //   i++;
              // }
              if (typeof value.totalPrice !== 'undefined' && value.Status !== 'NotAvailable') {
                value.PriceWithCurrency = Number(value.totalPrice).toLocaleString(tmsm_availpro_params.locale,
                  {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 2});

                value.Test = tmsm_availpro_params.locale;
                if (Number(value.totalPrice) === lowest_price) {
                  value.LowestPrice = 1;
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

      var setCalendarWidth = function () {

        if (tmsm_availpro_calendar.width() > 600) {
          tmsm_availpro_calendar.addClass('calendar-large');
          tmsm_availpro_calendar.removeClass('calendar-small');
        }
        else {
          tmsm_availpro_calendar.addClass('calendar-small');
          tmsm_availpro_calendar.removeClass('calendar-large');
        }
      };
      $(window).resize(function () {
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
          click: function (target) {

            var reorderdates = false;

            $('.day.mouseover').removeClass('mouseover');

            if (!$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {

              tmsm_availpro_calendar_lastdateclicked = target.date;

              // Reorder dates
              if (typeof tmsm_availpro_calendar_selected_begin !== 'undefined') {
                if (tmsm_availpro_calendar_selected_begin > tmsm_availpro_calendar_lastdateclicked) {

                  $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD')).removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass(
                    'selected-end').removeClass('active');

                  reorderdates = true;
                  tmsm_availpro_calendar_selected_begin = undefined;
                  tmsm_availpro_calendar_selected_end = undefined;
                }
                else {
                  reorderdates = false;
                }
              }
              else {
                reorderdates = false;
              }

              // Reinitialize selected days if begin and end have both been initialized
              if (typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined') {
                $('.day').removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
                tmsm_availpro_calendar_selected_begin = undefined;
                tmsm_availpro_calendar_selected_end = undefined;
                $('#tmsm-availpro-form-checkoutdateinfo').html('');
                $('#tmsm-availpro-form-checkoutdate').val('');
              }

              // Begin date not initialized
              if (typeof tmsm_availpro_calendar_selected_begin === 'undefined') {
                tmsm_availpro_calendar_selected_begin = tmsm_availpro_calendar_lastdateclicked;
                $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD')).addClass('selected selected-begin');
                $('#tmsm-availpro-form-checkindateinfo').html(tmsm_availpro_calendar_selected_begin.format('L'));
                $('#tmsm-availpro-form-checkindate').val(tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD'));
                $('#tmsm-availpro-form-arrivaldate').val(tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD'));

                tmsm_availpro_calendar_minstay = $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD') + ' .cell').data('minstay');
                if (!tmsm_availpro_calendar_minstay) {
                  tmsm_availpro_calendar_minstay = 0;
                }
                $('#tmsm-availpro-form-minstay-message').attr('data-value', tmsm_availpro_calendar_minstay);
                $('#tmsm-availpro-form-minstay-number').html(tmsm_availpro_calendar_minstay);

              }
              else {
                if (reorderdates === false) {
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
                  if (typeof tmsm_availpro_calendar_selected_end !== 'undefined') {
                    $('.calendar-day-' + tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD')).addClass('selected selected-end');
                  }
                }
              }

              // Calculate nights
              $('#tmsm-availpro-form-dates-container').hide();
              if (typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined') {
                tmsm_availpro_calendar_nights = tmsm_availpro_calendar_selected_end.diff(tmsm_availpro_calendar_selected_begin, "days");
                $('#tmsm-availpro-form-checkoutdateinfo').html(tmsm_availpro_calendar_selected_end.format('L'));
                $('#tmsm-availpro-form-checkoutdate').val(tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD'));

                $('#tmsm-availpro-form-dates-container').show();

                // Submit calculate total price
                $('#tmsm-availpro-calculatetotal').submit();
              }
              else {
                tmsm_availpro_calendar_nights = 0;
              }

              $('#tmsm-availpro-form-nights-number').html(tmsm_availpro_calendar_nights);
              $('#tmsm-availpro-form-nights-message').attr('data-value', tmsm_availpro_calendar_nights);
              $('#tmsm-availpro-form-nights').val(tmsm_availpro_calendar_nights);
              if (tmsm_availpro_calendar_nights > 0) {
                $('#tmsm-availpro-form-minstay-message').attr('data-value', 0);
                $('#tmsm-availpro-form-minstay-number').html('');
              }
              else {
                $('#tmsm-availpro-calculatetotal-totalprice').hide();
                $('#tmsm-availpro-calculatetotal-ota').hide();
              }

            }
          },

          onMonthChange: tmsm_availpro_calendar_set_events

        },
        doneRendering: function () {
          var self = this;
          $(this.element).on('mouseover', '.day:not(.inactive)', function (e) {

            var target = self.buildTargetObject(e.currentTarget, true);
            var hover_begin = tmsm_availpro_calendar_selected_begin;
            var hover_end = target.date;


            // Over Select
            // Begin date already initialized
            if (typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end === 'undefined') {

              $('.day').removeClass('mouseover').removeClass('selected').removeClass('selected-hover').removeClass('selected-end');

              $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('selected selected-end');

              // selection
              var i = 0;
              var selectedDate = hover_begin;
              if (hover_end > selectedDate) {
                $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');
                while (hover_end.format('YYYY-MM-DD') != selectedDate.format('YYYY-MM-DD')) {
                  i++;
                  selectedDate = moment(hover_begin).add(i, 'days');
                  $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');

                }

              }

            }
            // Begin date not initialized
            else {
              $('.day.mouseover').removeClass('mouseover');
              $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('mouseover');
            }

          });
        }

      });

      tmsm_availpro_calendar_set_events(tmsm_availpro_calendar_startdate);

      // Calculate total price form
      $('#tmsm-availpro-calculatetotal').on('submit', function (e) {
        e.preventDefault();

        // Reset value
        $('#tmsm-availpro-calculatetotal-totalprice-value').html('');
        $('#tmsm-availpro-calculatetotal-ota').html('');
        $('#tmsm-availpro-form-submit').prop('disabled', true);
        $('#tmsm-availpro-calculatetotal-loading').show();
        $('#tmsm-availpro-calculatetotal-errors').hide();
        $('#tmsm-availpro-form').removeClass('tmsm-availpro-form-has-ota-price');

        // Calculate date if begin and end are defined
        if (tmsm_availpro_calendar_selected_begin && tmsm_availpro_calendar_nights > 0) {

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

                if (data.data.accommodation && data.data.accommodation.totalprice) {
                  var Price = data.data.accommodation.totalprice;
                  if (Price) {
                    $('#tmsm-availpro-calculatetotal-totalprice').show();

                    var PriceWithCurrency = Price;

                    if (PriceWithCurrency) {
                      $('#tmsm-availpro-calculatetotal-totalprice').html(_.unescape(tmsm_availpro_params.i18n.selecteddatepricelabel.replace(/%/g, PriceWithCurrency)));
                    }
                  }
                }

                if (data.data.ota && data.data.ota.totalprice) {
                  var Price = data.data.ota.totalprice;
                  if (Price) {
                    $('#tmsm-availpro-form').addClass('tmsm-availpro-form-has-ota-price');

                    $('#tmsm-availpro-calculatetotal-ota').show();

                    var PriceWithCurrency = Price;
                    if (PriceWithCurrency) {
                      $('#tmsm-availpro-calculatetotal-ota').html('.' + _.unescape(tmsm_availpro_params.i18n.otacomparelabel.replace(/%/g, PriceWithCurrency)));
                    }
                  }
                  else {
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

    }

    // Room-specific calendar initialization for shortcode [tmsm-availpro-room-calendar]
    $('.tmsm-availpro-room-calendar').each(function() {
      var $calendar = $(this);
      var roomid = $calendar.data('roomid');
      var params_var = 'tmsm_availpro_room_' + roomid + '_params';

      // Check if room-specific params      console.log(params_var);
      console.log(window[params_var]);
      if (typeof window[params_var] === 'undefined') {
        console.log('Room calendar params not found for room ' + roomid);
        return;
      }
      
      var room_params = window[params_var];
      console.log('Initializing room calendar for room: ' + roomid);
      
      // Room calendar variables (similar to the main calendar)
      var calendar_today = moment().subtract(1, 'days');
      var calendar_startdate = moment();
      var calendar_enddate = moment().add(1, 'year');
      
      var calendar_selected_date;
      var calendar_selected_begin;
      var calendar_selected_end;
      var calendar_lastdateclicked;
      var calendar_current_year;
      var calendar_current_month;
      var calendar_nights = 1;
      var calendar_minstay = 0;
      
      /**
       * Set Calendar Events for Month
       */
      var calendar_set_events = function (month) {
        
        calendar_current_year = month.format('YYYY');
        calendar_current_month = month.format('MM');
        
        var events_toload = [];
        if (typeof room_params.data !== 'undefined') {
          
          if (typeof room_params.data[month.format('YYYY-MM')] !== 'undefined') {
            var days = room_params.data[month.format('YYYY-MM')];
            var events = [];
            var i = 0;
            var lowest_price = null;
            console.log('month' + month.format('YYYY-MM'));
            // Get lowest price
            $.each(days, function (index, value) {
              if (typeof value.totalPrice !== 'undefined' && value.status !== 'NotAvailable') {
                if (lowest_price === null) {
                  lowest_price = Number(value.totalPrice);
                }
                if (Number(value.totalPrice) < lowest_price) {
                  lowest_price = Number(value.totalPrice);
                }
              }
            });
            
            // Create Events
            $.each(days, function (index, value) {
              value.date = index;
              if (typeof value.totalPrice !== 'undefined' && value.status !== 'NotAvailable') {
                value.PriceWithCurrency = Number(value.totalPrice).toLocaleString(room_params.locale,
                  {style: "currency", currency: room_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 2});
                
                value.Test = room_params.locale;
                if (Number(value.totalPrice) === lowest_price) {
                  value.LowestPrice = 1;
                }
                events[i] = value;
                events_toload.push(events[i]);
                i++;
              }
            });
          }
        }
        return events_toload;
      };
      
      // Initialize the calendar with CLNDR
      var calendar_clndr = $calendar.clndr({
        template: $('#tmsm-availpro-room-calendar-template-' + roomid).html(),
        
        startWithMonth: calendar_startdate,
        constraints: {
          startDate: calendar_today,
          endDate: calendar_enddate
        },
     
        adjacentDaysChangeMonth: true,
        forceSixRows: false,
        trackSelectedDate: false,
        clickEvents: {
          click: function (target) {
            
            var reorderdates = false;
            $('.day.mouseover').removeClass('mouseover');
            if (!$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
              calendar_lastdateclicked = target.date;
              
              // Reorder dates if user clicks a date before the start date
              if (typeof calendar_selected_begin !== 'undefined') {
                if (calendar_selected_begin > calendar_lastdateclicked) {
                  $('.calendar-day-' + calendar_selected_begin.format('YYYY-MM-DD')).removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
                  reorderdates = true;
                  calendar_selected_begin = undefined;
                  calendar_selected_end = undefined;
                }
              }
              
              // Reinitialize if both dates are already selected (start new selection)
              if (typeof calendar_selected_begin !== 'undefined' && typeof calendar_selected_end !== 'undefined') {
                $('.day').removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
                calendar_selected_begin = undefined;
                calendar_selected_end = undefined;
                $('#tmsm-availpro-room-form-checkoutdateinfo-' + roomid).html('');
                $('#tmsm-availpro-room-form-checkoutdate-' + roomid).val('');
              }
              
              // First date selection (check-in)
              if (typeof calendar_selected_begin === 'undefined') {
                calendar_selected_begin = calendar_lastdateclicked;
                $('.calendar-day-' + calendar_selected_begin.format('YYYY-MM-DD')).addClass('selected selected-begin');
                $('#tmsm-availpro-room-form-checkindateinfo-' + roomid).html(calendar_selected_begin.format('L'));
                $('#tmsm-availpro-room-form-checkindate-' + roomid).val(calendar_selected_begin.format('YYYY-MM-DD'));
                $('#tmsm-availpro-room-form-arrivaldate-' + roomid).val(calendar_selected_begin.format('YYYY-MM-DD'));
                
                // Get minimum stay from the selected date
                calendar_minstay = $('.calendar-day-' + calendar_selected_begin.format('YYYY-MM-DD') + ' .cell').data('minstay');
                if (!calendar_minstay) {
                  calendar_minstay = 0;
                }
                $('#tmsm-availpro-room-form-minstay-message-' + roomid).attr('data-value', calendar_minstay);
                $('#tmsm-availpro-room-form-minstay-number-' + roomid).html(calendar_minstay);
              }
              // Second date selection (check-out)
              else {
                if (reorderdates === false) {
                  calendar_selected_end = calendar_lastdateclicked;
                  
                  // Check if dates respect minimum stay
                  if (calendar_minstay > 0) {
                    var checkminstay = moment(calendar_selected_begin);
                    if (checkminstay.add(calendar_minstay, 'days') > calendar_selected_end) {
                      console.warn('Selection does not respect minimum stay for room ' + roomid);
                      calendar_selected_end = undefined;
                    }
                  }
                  
                  if (typeof calendar_selected_end !== 'undefined') {
                    $('.calendar-day-' + calendar_selected_end.format('YYYY-MM-DD')).addClass('selected selected-end');
                  }
                }
              }
              
              // Calculate nights and update form
              $('#tmsm-availpro-room-form-dates-container-' + roomid).hide();
              if (typeof calendar_selected_begin !== 'undefined' && typeof calendar_selected_end !== 'undefined') {
                calendar_nights = calendar_selected_end.diff(calendar_selected_begin, "days");
                $('#tmsm-availpro-room-form-nights-' + roomid).val(calendar_nights);
                $('#tmsm-availpro-room-form-nights-message-' + roomid).attr('data-value', calendar_nights);
                $('#tmsm-availpro-room-form-nights-number-' + roomid).html(calendar_nights);
                $('#tmsm-availpro-room-form-checkoutdateinfo-' + roomid).html(calendar_selected_end.format('L'));
                $('#tmsm-availpro-room-form-checkoutdate-' + roomid).val(calendar_selected_end.format('YYYY-MM-DD'));
                $('#tmsm-availpro-room-form-dates-container-' + roomid).show();
                
                // Highlight the date range
                var range_begin = moment(calendar_selected_begin);
                while (range_begin < calendar_selected_end) {
                  range_begin.add(1, 'days');
                  if (range_begin < calendar_selected_end) {
                    $('.calendar-day-' + range_begin.format('YYYY-MM-DD')).addClass('selected');
                  }
                }
              }
            }
          }
        },
        doneRendering: function () {
          // Calendar rendered successfully
          console.log('Room calendar rendered for room: ' + roomid);
        },
        onMonthChange: function (month) {
          // Load events for the new month
          var events = calendar_set_events(month);
          console.log('events' + events);
          this.addEvents(events);
           
        }
        
      });
      
      // Load initial events for the starting month
      var initial_events = calendar_set_events(calendar_startdate);
      calendar_clndr.addEvents(initial_events);
    });

  }

  $( document ).ready( function() {
    TmsmAvailproApp.init();
  } );


  $( document ).on( 'elementor/popup/show', ( event, id, instance ) => {
    TmsmAvailproApp.init();
  } );

  // Display year best price in shortcode [tmsm-availpro-bestprice-year]
  var tmsm_availpro_bestprice_year = $('.tmsm-availpro-bestprice-year');
  if(tmsm_availpro_bestprice_year.length > 0){
    tmsm_availpro_bestprice_year.each(function(e){
      if($(this).data('price')){
        $(this).html(_.unescape(tmsm_availpro_params.i18n.yearbestpricelabel.replace('%',Number($(this).data('price')).toLocaleString(tmsm_availpro_params.locale, {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0}))));
      }
    });

  }

})(jQuery, TmsmAvailproApp);