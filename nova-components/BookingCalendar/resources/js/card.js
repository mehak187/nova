import VueTippy from "vue-tippy";
import Card from './components/Card'
// import BookingCalendarV2 from './components/BookingCalendarV2'

// load vue-tippy css
import "tippy.js/dist/tippy.css";

Nova.booting((app, store) => {
  app.component('booking-calendar', Card)
//   app.component('booking-calendar-view-v2', BookingCalendarV2)

  app.use(VueTippy);
})
