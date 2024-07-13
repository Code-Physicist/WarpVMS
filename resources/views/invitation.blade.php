@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Invitations
@stop
@section('sub_header')
Click on calendar to invite visitors
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/full-calendar.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/full-calendar.custom.css') }}" />
@stop
@section('content')
<div id="app">
    <div v-show="active_ui === 1" class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Day/Month/Year</h5>
                        <button @click="show_create" class="btn btn-danger"><span class="icon-add"></span> Invite&nbsp;</button>
                    </div>
                    <div>
                        <div ref="my_calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-show="active_ui === 2" class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Invite visitors</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div>
                        <div class="d-flex justify-content-center my-3">
                            <button type="button" @click="active_ui = 1" class="btn btn-outline-secondary mx-2">Cancel</button> <button type="button" @click="submit" class="btn btn-danger mx-2">Submit</button>
                        </div>
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
                        Modal title
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">...</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        Understood
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
        active_ui: 1,
        calendar: null,
        my_modal: null,
        invitation: {

        },
        invitations: [],
      }
    },
    mounted() {
        var calendarEl = this.$refs.my_calendar;
        var calendar = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
			left: "prev,next today",
			center: "title",
			right: "dayGridMonth,timeGridWeek,timeGridDay",
		},
		initialDate: "2022-10-12",
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

    this.my_modal = new bootstrap.Modal(this.$refs.my_modal, { keyboard: false });
    },
    methods: {
        submit() {

        },
        show_create(arg) {
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
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop