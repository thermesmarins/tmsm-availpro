(function( $ ) {
	'use strict';

  var tmsm_availpro_calendar_today = moment();
  var tmsm_availpro_calendar_startdate = moment();
  var tmsm_availpro_calendar_enddate = moment().add(1, 'year');

  console.log(tmsm_availpro_calendar_startdate);

  var tmsm_availpro_calendar_selected_date;
  var tmsm_availpro_calendar_selected_begin;
  var tmsm_availpro_calendar_selected_end;
  var tmsm_availpro_calendar_lastdateclicked;
  var tmsm_availpro_calendar_current_year;
  var tmsm_availpro_calendar_current_month;
  var tmsm_availpro_calendar_nights;

  /**
   * Set Calendar Events for Month
   *
   * @param month
   */
  var tmsm_availpro_calendar_set_events = function(month){

    //console.log(month);
    console.log('you just went to ' + month.format('MMMM, YYYY'));
    //console.log('onMonthChange');
    /*if(calendarOldTarget){
     target = calendarOldTarget;
     removeSelectedDays();
     //addSelectedDays(target);
     }*/

    tmsm_availpro_calendar_current_year = month.format('YYYY');
    tmsm_availpro_calendar_current_month = month.format('MM');

    console.log('tmsm_availpro_calendar_current_year: '+tmsm_availpro_calendar_current_year);
    console.log('tmsm_availpro_calendar_current_month: '+tmsm_availpro_calendar_current_month);

    var events_toload = [];
    if (typeof tmsm_availpro_params.data !== 'undefined') {
      //console.log(month.format('YYYY-MM'));
      //console.log(tmsm_availpro_params.data[month.format('YYYY-MM')]);
      if (typeof tmsm_availpro_params.data[month.format('YYYY-MM')] !== 'undefined') {
        var days = tmsm_availpro_params.data[month.format('YYYY-MM')];
        var events = [];
        var i = 0;
        var lowest_price = null;

        // Get lowest price
        $.each(days, function (index, value) {
          if (typeof value.Price !== 'undefined') {
            lowest_price = (value.Price);
            if(lowest_price > (value.Price)){
              lowest_price = (value.Price);
            }
          }
        });

        // Create Events
        $.each(days, function (index, value) {
          //console.log(index);
          value.date = index;
          if (typeof value.Price !== 'undefined') {
            value.PriceWithCurrency = Number(value.Price).toLocaleString(tmsm_availpro_params.locale,
              {style: "currency", currency: tmsm_availpro_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});

            if(value.Price === lowest_price){
              value.LowestPrice=1;
            }
          }

          events[i] = value;
          events_toload.push(events[i]);
          i++;
        });
        console.log('lowest_price: '+lowest_price);
        tmsm_availpro_calendar_clndr.addEvents(events_toload);
      }

    }


    // reassign previous selection
    if(tmsm_availpro_calendar_lastdateclicked != null){
      //console.log('lastdateclicked: '+clndr_lastdateclicked.date.format('YYYY-MM-DD'));
      /*tmsm_availpro_calendar_selected_begin = tmsm_availpro_calendar_lastdateclicked.date;
      tmsm_availpro_calendar_selected_end = tmsm_availpro_calendar_lastdateclicked.date;

      if(tmsm_availpro_calendar_lastdateclicked.events.length > 0){
        tmsm_availpro_calendar_selected_end = tmsm_availpro_calendar_lastdateclicked.events[0].date_end;
        tmsm_availpro_calendar_selected_end = moment(tmsm_availpro_calendar_selected_end);
      }*/

      // selection
      /*i = 0;
      var tmsm_availpro_calendar_selected_date = tmsm_availpro_calendar_selected_begin;
      $('.calendar-day-' + tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')).addClass('selected active');
      while(tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD') != tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')) {
        i++;
        tmsm_availpro_calendar_selected_date = moment(tmsm_availpro_calendar_selected_begin).add(i, 'days');
        $('.calendar-day-' + tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')).addClass('selected');

      }*/
    }

  }

  var tmsm_availpro_calendar = $('#tmsm-availpro-calendar');
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
        console.log('click');
        //console.log(target);

        $('.day.mouseover').removeClass('mouseover');

        if(target.events.length && !$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
          //$('.day').removeClass('selected').removeClass('selected-range').removeClass('active');

          tmsm_availpro_calendar_lastdateclicked = target.date;

          // Reinitialize selected days if begin and end have both been initialized
          if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined'){
            $('.day').removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
            tmsm_availpro_calendar_selected_begin = undefined;
            tmsm_availpro_calendar_selected_end = undefined;
          }

          // Begin date not initialized
          if(typeof tmsm_availpro_calendar_selected_begin === 'undefined'){
            tmsm_availpro_calendar_selected_begin = tmsm_availpro_calendar_lastdateclicked;
            $('.calendar-day-' + tmsm_availpro_calendar_selected_begin.format('YYYY-MM-DD')).addClass('selected selected-begin');
          }
          else{
              tmsm_availpro_calendar_selected_end = tmsm_availpro_calendar_lastdateclicked;
              $('.calendar-day-' + tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD')).addClass('selected selected-end');
            /*var tmsm_availpro_calendar_selected_hover = tmsm_availpro_calendar_selected_begin;
            while(tmsm_availpro_calendar_selected_hover.format('YYYY-MM-DD') != tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD')) {
              tmsm_availpro_calendar_selected_hover.add(1, 'days');
              //console.log('tmsm_availpro_calendar_selected_begin_hover:');
              //console.log(tmsm_availpro_calendar_selected_begin_hover);
              $('.calendar-day-' + tmsm_availpro_calendar_selected_hover.format('YYYY-MM-DD')).addClass('selected selected-range');
            }*/
          }

          // Reorder dates
          if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end !== 'undefined'){
            if(tmsm_availpro_calendar_selected_begin > tmsm_availpro_calendar_selected_end){
            var temp_begin = tmsm_availpro_calendar_selected_begin;
              tmsm_availpro_calendar_selected_begin = tmsm_availpro_calendar_selected_end;
              tmsm_availpro_calendar_selected_end = temp_begin;
            }
            tmsm_availpro_calendar_nights = tmsm_availpro_calendar_selected_end.diff(tmsm_availpro_calendar_selected_begin, "days");

          }

          console.log('tmsm_availpro_calendar_selected_begin:');
          console.log(tmsm_availpro_calendar_selected_begin);
          console.log('tmsm_availpro_calendar_selected_end:');
          console.log(tmsm_availpro_calendar_selected_end);
          console.log('nights: ' + tmsm_availpro_calendar_nights);
          //$('.calendar-day-' + tmsm_availpro_calendar_lastdateclicked.format('YYYY-MM-DD')).addClass('selected active');

          // selection
          /*var i = 0;
          tmsm_availpro_calendar_selected_date = tmsm_availpro_calendar_selected_begin;
          $('.calendar-day-' + tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')).addClass('selected active');
          while(tmsm_availpro_calendar_selected_end.format('YYYY-MM-DD') != tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')) {
            i++;
            tmsm_availpro_calendar_selected_date = moment(tmsm_availpro_calendar_selected_begin).add(i, 'days');
            $('.calendar-day-' + tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')).addClass('selected');
          }*/

          // date
          //$('.duration .date-from .value', clndr_summary).text(tmsm_availpro_calendar_selected_begin.format("ddd") + ' '+ tmsm_availpro_calendar_selected_begin.format("LL"));
          //$('.duration .date-to .value', clndr_summary).text(tmsm_availpro_calendar_selected_end.format("ddd") + ' '+ tmsm_availpro_calendar_selected_end.format("LL"));

        }
      },

      onMonthChange: tmsm_availpro_calendar_set_events,

      today: function(month) {
        console.log('today ' + month.format('MMMM, YYYY'));
      },
    },
    doneRendering: function() {
      var self = this;
      $(this.element).on('mouseover', '.day.event:not(.inactive)', function(e) {

        var target = self.buildTargetObject(e.currentTarget, true);
        var hover_begin = tmsm_availpro_calendar_selected_begin;
        var hover_end = target.date;


        // Over Select
        // Begin date already initialized
        if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_selected_end === 'undefined'){

          //console.log(target);

          //if(target.events.length > 0){
          //  selectedEnd = target.events[0].date_end;
          //  selectedEnd = moment(selectedEnd);
          //}
          //hover_end = moment(hover_begin).add(3, 'days');

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



        //var target = self.buildTargetObject(e.currentTarget, true);
        //console.log(target);
        //tmsm_availpro_calendar_selected_date = target.date;
        //var tmsm_availpro_calendar_hover = target.date;

        /*if(typeof tmsm_availpro_calendar_selected_begin !== 'undefined' && typeof tmsm_availpro_calendar_hover !== 'undefined' && tmsm_availpro_calendar_hover != tmsm_availpro_calendar_selected_begin){

          var tmsm_availpro_calendar_selected_hover = tmsm_availpro_calendar_selected_begin;
          while(tmsm_availpro_calendar_selected_hover.format('YYYY-MM-DD') != tmsm_availpro_calendar_hover.format('YYYY-MM-DD')) {
            console.log(moment());
            tmsm_availpro_calendar_selected_hover.add(1, 'days');
            //console.log('tmsm_availpro_calendar_selected_begin_hover:');
            //console.log(tmsm_availpro_calendar_selected_begin_hover);
            $('.calendar-day-' + tmsm_availpro_calendar_selected_hover.format('YYYY-MM-DD')).addClass('selected selected-range');

          }*/

          //$('.day').removeClass('selected-range');
          //console.log('tmsm_availpro_calendar_selected_date:');
          //console.log(tmsm_availpro_calendar_selected_date);

          //console.log('tmsm_availpro_calendar_selected_begin:');
          //console.log(tmsm_availpro_calendar_selected_begin);
          /*if(tmsm_availpro_calendar_selected_begin != tmsm_availpro_calendar_selected_date){
            var tmsm_availpro_calendar_selected_begin_hover = tmsm_availpro_calendar_selected_begin;
            while(tmsm_availpro_calendar_selected_begin_hover.format('YYYY-MM-DD') != tmsm_availpro_calendar_selected_date.format('YYYY-MM-DD')) {
              tmsm_availpro_calendar_selected_begin_hover = tmsm_availpro_calendar_selected_begin_hover.add(1, 'days');
              console.log('tmsm_availpro_calendar_selected_begin_hover:');
              console.log(tmsm_availpro_calendar_selected_begin_hover);
              $('.calendar-day-' + tmsm_availpro_calendar_selected_begin_hover.format('YYYY-MM-DD')).addClass('selected-range');

            }
          }*/


        //}
      });
    }

  });

  tmsm_availpro_calendar_set_events(tmsm_availpro_calendar_startdate);

})( jQuery );
