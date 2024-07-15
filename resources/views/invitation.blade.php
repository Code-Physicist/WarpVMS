@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Invitations
@stop
@section('sub_header')
Invite visitors, update schedules and resend invitation emails
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/daterange.css') }}" />
<link rel="stylesheet" href="{{ asset('css/full-calendar.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/full-calendar.custom.css') }}" />
@stop
@section('content')
<div id="app">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div>
                        <div ref="my_calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" ref="my_modal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        Invite Visitors
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
					<div class="row">
                		<div class="mb-3">
                  			<label class="form-label">Department *</label>
                  			<select v-model="invitation.dept_id" class="form-select">
                      			<option value="0">Please select</option>
                      			<option v-for="d in depts" :value="d.dept_id">
                        			{d.full_name}
                      			</option>
                 			 </select>
                		</div>
              		</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="mb-3">
								<label class="form-label" for="abc">Start Date</label>
                        		<input type="text" class="form-control" id="start_date">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="mb-3">
								<label class="form-label" for="abc">End Date</label>
                        		<input type="text" class="form-control" id="end_date">
							</div>
						</div>
                    </div>
					<div class="row">
						<div class="col">
							
								<label class="form-label">Interval for each day</label>
								<div class="row">
									<div class="col-sm-6">
										<div class="mb-3">
											<input type="text" class="form-control" id="interval">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-check mb-3">
                        					<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                        					<label class="form-check-label" for="flexCheckChecked">All day</label>
                      					</div>
									</div>
								</div>

						</div>
                    </div>
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button @click="submit" type="button" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<!-- For using Modal-->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterange.js') }}"></script>
<script src="{{ asset('js/full-calendar.min.js') }}"></script>
<script>
//External function
function test() {
    alert("Yo");
}
const { createApp } = Vue;
createApp({
    data() {
      return {
        m_active_ui: 1,
        calendar: null,
        my_modal: null,
		depts:[],
        invitation: {
			dept_id: 0,
			visitors: [],
			start_date: "",
			end_date: "",
			start_time: "",
			end_time:""
        },
        invitations: [],
      }
    },
    mounted() {
		this.init_ui();

		$("#start_date").daterangepicker({
  			singleDatePicker: true,
  			startDate: moment().startOf("hour"),
  			endDate: moment().startOf("hour").add(32, "hour"),
  			locale: {
    			format: "DD/MM/YYYY",
  			},
		});

		$("#end_date").daterangepicker({
  			singleDatePicker: true,
  			startDate: moment().startOf("hour"),
  			endDate: moment().startOf("hour").add(32, "hour"),
  			locale: {
    			format: "DD/MM/YYYY",
  			},
		});

		$("#interval").daterangepicker({
    		timePicker: true,
    		timePicker24Hour: true,
    		timePickerIncrement: 5,
    		locale: {
      			format: "HH:mm",
    		},
  		})
  		.on("show.daterangepicker", function (ev, picker) {
    		picker.container.find(".calendar-table").hide();
  		});
		//Initialize Calendar
        var calendarEl = this.$refs.my_calendar;
        var calendar = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
			left: "prev,next today",
			center: "title",
			right: "dayGridMonth,timeGridWeek,timeGridDay",
		},
		//initialDate: "2022-10-12",
		initialDate: Date.now(),
		navLinks: true, // can click day/week names to navigate views
		selectable: true,
		selectMirror: true,
		select: this.show_create,
		eventClick: function (arg) {
            console.log(arg.event);
			if (confirm("Are you sure you want to delete this event?")) {
				arg.event.remove();
			}
		},
		editable: true,
		dayMaxEvents: true, // allow "more" link when too many events
		events: [
			{
				title: "All Day Event",
				start: "2022-10-01",
				color: "#e6f5e3",
				borderColor: "#41ab2e",
				textColor: "#41ab2e",
			},
			{
				title: "Long Event",
				start: "2022-10-07",
				end: "2022-10-10",
				color: "#eaf1ff",
				borderColor: "#0a50d8",
				textColor: "#0a50d8",
			},
			{
				groupId: 999,
				title: "Birthday",
				start: "2022-10-09T16:00:00",
				color: "#e2e5ec",
				borderColor: "#434957",
				textColor: "#434957",
			},
			{
				groupId: 999,
				title: "Birthday",
				start: "2022-10-16T16:00:00",
				color: "#ffebeb",
				borderColor: "#ce385b",
				textColor: "#ce385b",
			},
			{
				title: "Conference",
				start: "2022-10-11",
				end: "2022-10-13",
				color: "#e8f4d6",
				borderColor: "#53810c",
				textColor: "#53810c",
			},
			{
				title: "Meeting",
				start: "2022-10-14T10:30:00",
				end: "2022-10-14T12:30:00",
				color: "#e8f4ff",
				borderColor: "#0b5aa9",
				textColor: "#0b5aa9",
			},
			{
				title: "Lunch",
				start: "2022-10-16T12:00:00",
				color: "#fff5d5",
				borderColor: "#d49d1c",
				textColor: "#d49d1c",
			},
			{
				title: "Meeting",
				start: "2022-10-18T14:30:00",
				color: "#e2ddff",
				borderColor: "#7164b5",
				textColor: "#7164b5",
			},
			{
				title: "Interview",
				start: "2022-10-21T17:30:00",
				color: "#ffe6fe",
				borderColor: "#780974",
				textColor: "#780974",
			},
			{
				title: "Meeting",
				start: "2022-10-22T20:00:00",
				color: "#ffeade",
				borderColor: "#d45c1c",
				textColor: "#d45c1c",
			},
			{
				title: "Birthday",
				start: "2022-10-13T07:00:00",
				color: "#dde3c9",
				borderColor: "#93b711",
				textColor: "#93b711",
			},
			{
				title: "Click for Google",
				url: "https://bootstrap.gallery/",
				start: "2022-10-28",
				color: "#e4e7ed",
				borderColor: "#294e9d",
				textColor: "#294e9d",
			},
			{
				title: "Interview",
				start: "2022-10-20",
				color: "#ece0d9",
				borderColor: "#c55513",
				textColor: "#c55513",
			},
			{
				title: "Product Launch",
				start: "2022-10-29",
				color: "#eedee6",
				borderColor: "#b32268",
				textColor: "#b32268",
			},
			{
				title: "Leave",
				start: "2022-10-25",
				color: "#dfffe1",
				borderColor: "#1c8b24",
				textColor: "#1c8b24",
			},
		],
	});
    this.calendar = calendar;
    this.calendar.render();

	//Initialize modal
    this.my_modal = new bootstrap.Modal(this.$refs.my_modal, { keyboard: false });
    },
    methods: {
		async init_ui() {
            try {
          	    await this.get_invitation_depts();
            }
            catch(error) {
                console.log(error);
            }
        
        },
        submit() {
			alert("yo");
        },
        show_create(arg) {
			console.log(arg);
            //this.active_ui = 2;
            /*this.calendar.removeAllEvents();
            this.calendar.addEvent(
                {
                    id: 10,
				    title: "Yo",
				    start: "2022-10-24",
				    color: "#dfffe1",
				    borderColor: "#1c8b24",
				    textColor: "#1c8b24",
			    },
            );
            console.log(this.calendar);
            */
            this.calendar.unselect();
            this.my_modal.show();
        },
		async get_invitation_depts() {
          const response = await axios.post("/admin/get_invitation_depts");
          this.depts = response.data.data_list;
        },
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop